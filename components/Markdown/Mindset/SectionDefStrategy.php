<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Mindset;

use Commune\Blueprint\Exceptions\Runtime\BrokenSessionException;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Components\Markdown\Analysers\MessageAnalyser;
use Commune\Components\Markdown\Exceptions\MarkdownOptNotFoundException;
use Commune\Components\Markdown\MarkdownComponent;
use Commune\Components\Markdown\Options\MDGroupOption;
use Commune\Message\Host\Convo\Verbal\MarkdownMsg;
use Commune\Support\Markdown\Data\MDSectionData;
use Commune\Support\Markdown\MarkdownUtils;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Utils\TypeUtils;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SectionDefStrategy
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
     * SectionDefStrategy constructor.
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

        $this->sendMessage($group, $section, $dialog);

        return $this->doAwait($def, $group, $section, $dialog);
    }

    protected function doAwait(
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
        MDSectionData $section,
        Dialog $dialog
    ) : ? Operator
    {
        $text = trim($section->text);
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

                    continue;
                }
            }

            // buffers 处理.
            $buffers[] = $line;
        }

        if (!empty($buffers)) {
            $text = implode(PHP_EOL, $buffers);
            $dialog->send()->message(MarkdownMsg::instance($text))->over();
        }

        return null;
    }

    /*-------- receive ---------*/

    protected function onReceive(SectionStageDef $def, Dialog\Receive $dialog) : Operator
    {
        return $dialog->confuse();
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