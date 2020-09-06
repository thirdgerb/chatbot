<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\DefStrategy;

use Commune\Blueprint\Exceptions\Runtime\BrokenSessionException;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Await;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Components\Markdown\Analysers\AwaitAnalyser;
use Commune\Components\Markdown\Analysers\MessageAnalyser;
use Commune\Components\Markdown\Constants\MDContextLang;
use Commune\Components\Markdown\Exceptions\MarkdownOptNotFoundException;
use Commune\Components\Markdown\MarkdownComponent;
use Commune\Components\Markdown\Mindset\SectionStageDef;
use Commune\Components\Markdown\Options\MDGroupOption;
use Commune\Message\Host\Convo\Verbal\MarkdownMsg;
use Commune\Protocals\HostMsg\Convo\QA\Step;
use Commune\Support\Markdown\Data\MDSectionData;
use Commune\Support\Markdown\MarkdownUtils;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Utils\TypeUtils;

/**
 * Markdown 文档转 stage 的解析策略.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SectionStrategy
{

    /**
     * @var MarkdownComponent
     */
    protected $component;

    /**
     * @var OptRegistry
     */
    protected $registry;

    /**
     * SectionStrategy constructor.
     * @param MarkdownComponent $component
     * @param OptRegistry $registry
     */
    public function __construct(MarkdownComponent $component, OptRegistry $registry)
    {
        $this->component = $component;
        $this->registry = $registry;
    }


    public function __invoke(Dialog $dialog) : ? Operator
    {
        $def = $dialog->ucl->findStageDef($dialog->cloner);
        if (!$def instanceof SectionStageDef) {
            $expect = SectionStageDef::class;
            $actual = TypeUtils::getType($def);
            throw new BrokenSessionException(
                "invalid stage def, expect $expect, actual $actual"
            );
        }


        if ($dialog instanceof Dialog\Activate) {
            return $this->onActivate($def, $dialog);

        } elseif ($dialog instanceof Dialog\Receive) {
            return $this->onReceive($def, $dialog);

        } elseif ($dialog instanceof Dialog\Resume) {
            return $this->onResume($def, $dialog);

        } else {
            return null;
        }
    }

    /*-------- activate ---------*/

    protected function onActivate(SectionStageDef $def, Dialog\Activate $dialog) : Operator
    {
        $group = $this->getGroupOption($def->groupName);
        $section = $this->getSectionOption($def->contextName, $def->orderId);

        // 每一个 stage 都可以拆分成多段对话
        // 在内部切换时会保留上下文记忆, 避免重复.
        $await = $dialog->process->getAwait();
        $current = $dialog->ucl;
        $isSameContext = isset($await) && $await->isSameContext($current);
        if (! $isSameContext) {
            $this->sendTitle($def, $dialog);
            $dialog->context[$def->stageName] = 0;
        }


        // 通过计数器了解当前要输出的片段.
        $max = count($section->texts) - 1;
        $current = $dialog->context[$def->stageName] ?? 0;

        // 逐行扫描发现的动态注释.
        $comments = [];
        $operator = null;

        $textIndex = $current > $max ? $max : $current;
        $text = $section->texts[$textIndex] ?? '';
        $text = trim($text);
        $moreThenEnd = $current > $max;
        $cloner = $dialog->cloner;
        if ($moreThenEnd) {
            $this->sendTitle($def, $dialog);
            $cloner->silence($moreThenEnd);
        }
        $operator = $this->sendMessage(
            $group,
            $text,
            $dialog,
            $comments
        );
        $cloner->silence(false);

        // 表示来过当前节点了. 如果没有 askStepper 重置， 过了最后一个节点就不会再发消息
        $dialog->context[$def->stageName] = $current + 1;

        if (isset($operator)) {
            return $operator;
        }

        if ($current < $max) {
            $await = $this->defaultContinue($dialog, $current, $max);
        } else {
            $await = $this->defaultAwait($def, $dialog);
        }

        return $this->askAwait(
            $await,
            $def,
            $group,
            $dialog,
            $comments
        );
    }

    protected function sendTitle(
        SectionStageDef $def,
        Dialog $dialog
    ) : void
    {
        $level = $def->depth;

        $prefix = implode('', array_fill(0, $level, '#' ));
        $dialog
            ->send()
            ->info(MDContextLang::LOCATION, [
                'title' => $prefix. ' ' . $def->getTitle(),
            ])
            ->over();
    }

    protected function defaultContinue(
        Dialog $dialog,
        int $current,
        int $max
    ) : Operator
    {
        return $dialog
            ->await()
            ->askStepper(
                MDContextLang::ASK_CHOOSE,
                $current,
                $max
            );
    }


    protected function defaultAwait(
        SectionStageDef $def,
        Dialog\Activate $dialog
    )
    {
        $await = $dialog->await();
        $await->askChoose(MDContextLang::ASK_CHOOSE);
        $question = $await->getCurrentQuestion();

        $parent = $def->parent;
        $children = $def->children;

        $ucl = $dialog->ucl;
        foreach ($children as $route) {
            $choice = $ucl->goStage($route);
            $cloner = $dialog->cloner;
            $question->addSuggestion(
                $choice->findStageDef($cloner)->getDescription(),
                null,
                $choice
            );
        }
        if (isset($parent)) {
            $question->addSuggestion(
                MDContextLang::BACKWARD
                ,
                'b',
                $ucl->goStage($parent)
            );
        }

        return $await;
    }


    protected function askAwait(
        Await $await,
        SectionStageDef $def,
        MDGroupOption $group,
        Dialog\Activate $dialog,
        array &$comments
    ) : Operator
    {
        if (empty($comments)) {
            return $await;
        }

        $awaitAlsMap = $group->getAnalyserMapByInterface(
            AwaitAnalyser::class,
            false
        );

        if (empty($awaitAlsMap)) {
            return $await;
        }

        // 遍历所有的动态注解.
        $container = $dialog->container();
        foreach ($comments as list($comment, $content)) {
            if (array_key_exists($comment, $awaitAlsMap)) {
                /**
                 * @var AwaitAnalyser $als
                 */
                $alsName = $awaitAlsMap[$comment];
                $als = $container->make($alsName);
                $await = $als($dialog, $def, $content, $await);

                if (!$await instanceof Await) {
                    return $await;
                }
            }
        }

        return $await;
    }


    protected function sendMessage(
        MDGroupOption $group,
        string $text,
        Dialog $dialog,
        array &$comments
    ) : ? Operator
    {
        $lines = explode(PHP_EOL, $text);
        $buffers = [];

        $messageAlsMap = $group->getAnalyserMapByInterface(
            MessageAnalyser::class,
            false
        );

        foreach ($lines as $line) {

            $commentInfo = MarkdownUtils::parseCommentLine($line);

            // 如果有注释
            if (!empty($commentInfo)) {
                $comments[] = $commentInfo;
                list($comment, $content) = $commentInfo;
                $analyserName = $messageAlsMap[$comment] ?? null;

                if (!empty($analyserName)) {

                    /**
                     * @var MessageAnalyser $analyser
                     */
                    $analyser = $dialog->container()->make($analyserName);
                    // 默认返回的是 null. 如果中途要故意中断, 那就返回 operator
                    $operator = $analyser($content, $buffers, $dialog);
                    if (isset($operator)) {
                        return $operator;
                    }
                }
            } else {
                // buffers 处理.
                $buffers[] = $line;
            }

        }

        if (!empty($buffers)) {
            $text = implode(PHP_EOL, $buffers);
            $text = trim($text);
            if (!empty($text)) {
                $dialog->send()->message(MarkdownMsg::instance($text))->over();
            }
        }

        return null;
    }

    /*-------- receive ---------*/

    protected function onReceive(SectionStageDef $def, Dialog\Receive $dialog) : Operator
    {
        $name = $def->stageName;
        return $dialog
            ->hearing()
            ->isAnswerOf(Step::class)
            ->then(function(Dialog $dialog, Step $answer) use ($name){
                $dialog->context[$name] = $answer->getStep();
                return $dialog->reactivate();
            })
            ->end();
    }

    protected function onResume(SectionStageDef $def, Dialog\Resume $dialog) : ? Operator
    {
        return null;
    }


    /*-------- helps ---------*/

    protected function getSectionOption(
        string $markdownId,
        string $orderId
    ) : MDSectionData
    {
        $optionId = "$markdownId.$orderId";
        /**
         * @var MDSectionData $option
         */
        $option = $this->registry
            ->getCategory(MDSectionData::class)
            ->find($optionId);

        return $option;
    }

    protected function getGroupOption(string $groupName) : MDGroupOption
    {
        $option = $this->component->getGroupOptionByName($groupName);
        if (empty($option)) {
            throw new MarkdownOptNotFoundException(
                " markdown component group option $groupName not found"
            );
        }
        return $option;
    }
}