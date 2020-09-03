<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Analysers\Await;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Await;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Components\Markdown\Mindset\SectionStageDef;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RouteToRelationAls extends AbsAwaitAnalyser
{
    const PARENT = 'parent';

    const CHILDREN = 'children';

    const BROTHERS = 'brothers';

    const NEXT = 'next';

    public function __invoke(
        Dialog $dialog,
        SectionStageDef $def,
        string $content,
        Await $await
    ): ? Operator
    {
        list($first, $index) = $this->separateRouteAndIndex($content);

        $question = $await->getCurrentQuestion();
        if (empty($question)) {
            return $await;
        }

        switch ($first) {
            case self::PARENT :
                $parent = $def->parent;
                if (empty($parent)) {
                    return $await;
                }

                $ucl = $dialog->ucl->goStage($parent);
                list($index, $suggestion) = $this->parseIndexAndDesc(
                    $ucl,
                    $dialog->cloner,
                    $index
                );
                $question->addSuggestion(
                    $suggestion,
                    $index,
                    $ucl
                );
                return $await;

            case self::CHILDREN:
                $children = $def->children;
                if (empty($children)) {
                    return $await;
                }

                $current = $dialog->ucl;
                $cloner = $dialog->cloner;
                foreach ($children as $child) {
                    $ucl = $current->goStage($child);
                    list ($index, $suggestion) = $this->parseIndexAndDesc(
                        $ucl,
                        $cloner,
                        ''
                    );
                    $question->addSuggestion($suggestion, $index, $ucl);
                }
                return $await;
            case self::NEXT:
                $current = $dialog->ucl;
                $next = $this->getNextStage($dialog, $def, true);
                if (empty($next)) {
                    return $await;
                }

                $nextUcl = $current->goStage($next->getStageShortName());
                $cloner = $dialog->cloner;
                list($index, $suggestion) = $this->parseIndexAndDesc(
                    $nextUcl,
                    $cloner,
                    $index
                );

                $question->addSuggestion($suggestion, $index, $nextUcl);
                return $await;

            case self::BROTHERS:

                $parent = $def->parent;
                if (empty($parent)) {
                    return $await;
                }
                $current = $dialog->ucl;
                $cloner = $dialog->cloner;
                $parentUcl = $current->goStage($parent);
                $parentDef = $this->getSectionStageDef($parentUcl, $cloner);
                $children = $parentDef->children;
                $self = array_search($current->stageName, $children);
                unset($children[$self]);
                $younger = $def->younger;
                if (isset($younger)) {
                    $key = array_search($younger, $children);
                    unset($children[$key]);
                    array_unshift($children, $younger);
                }

                foreach ($children as $child) {
                    $ucl = $current->goStage($child);
                    list ($index, $suggestion) = $this->parseIndexAndDesc(
                        $ucl,
                        $cloner,
                        ''
                    );
                    $question->addSuggestion($suggestion, $index, $ucl);
                }
                return $await;

            default:
                return $await;
        }


    }


}