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
use Commune\Blueprint\Ghost\Operate\Operator;
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

        $isSameContext = $dialog->process->getAwait()->isSameContext($dialog->ucl);
        if (! $isSameContext) {
            $dialog->context[$def->stageName] = 0;
        }

        // 通过计数器了解当前要输出的片段.
        $max = count($section->texts) - 1;
        $current = $dialog->context[$def->stageName] ?? 0;

        $operator = null;
        if ($current <= $max) {
            $text = $section->texts[$current];
            $text = trim($text);
            $operator = $this->sendMessage($group, $text, $dialog);
        }

        if (isset($operator)) {
            return $operator;
        }

        if ($current < $max) {
            return $this->askContinue($dialog, $current, $max);
        } else {
            return $this->askAwait($def, $group, $section, $dialog);
        }
    }

    protected function askContinue(
        Dialog $dialog,
        int $current,
        int $max
    ) : Operator
    {
        return $dialog
            ->await()
            ->askStepper(
                MDContextLang::ASK_CONTINUE,
                $current,
                $max
            );
    }

    protected function askAwait(
        SectionStageDef $def,
        MDGroupOption $group,
        MDSectionData $section,
        Dialog\Activate $dialog
    ) : Operator
    {
        $await = $this->defaultAwait($def, $dialog);


        // todo ...

        return $await;
    }

    protected function defaultAwait(
        SectionStageDef $def,
        Dialog\Activate $dialog
    )
    {

        $await = $dialog->await();
        $await->askChoose("请选择");
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
            $question->addSuggestion('返回', 'b', $ucl->goStage($parent));
        }

        return $await;
    }


    protected function sendMessage(
        MDGroupOption $group,
        string $text,
        Dialog $dialog
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