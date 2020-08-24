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

use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Components\Markdown\Analysers\AwaitAnalyser;
use Commune\Components\Markdown\Mindset\SectionStageDef;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsAwaitAnalyser implements AwaitAnalyser
{


    public function getNextStage(
        Dialog $dialog,
        SectionStageDef $current,
        bool $fromChildren = true
    ) : ? SectionStageDef
    {
        $cloner = $dialog->cloner;
        $children = $current->children;
        if ($fromChildren && !empty($children)) {
            $ucl = $dialog->ucl->goStage($children[0]);
            return $this->getSectionStageDef($ucl, $cloner);
        }

        $younger = $current->younger;
        if (!empty($younger)) {
            $ucl = $dialog->ucl->goStage($younger);
            return $this->getSectionStageDef($ucl, $cloner);
        }

        $parent = $current->parent;
        // 选择父节点的后续节点
        if (!empty($parent)) {
            $ucl = $dialog->ucl->goStage($younger);
            $def = $this->getSectionStageDef($ucl, $cloner);
            return $this->getNextStage($dialog, $def, false);
        }

        return null;
    }

    protected function getSectionStageDef(Ucl $ucl, Cloner $cloner) : SectionStageDef
    {
        $def = $ucl->findStageDef($cloner);
        if ($def instanceof SectionStageDef) {
            return $def;
        }

        throw new CommuneLogicException(
            "target $ucl stage def is not " . SectionStageDef::class
        );
    }

    protected function parseIndexAndDesc(Ucl $ucl, Cloner $cloner, string $index) : array
    {
        $indexParts = explode('|', $index);
        $index = empty($indexParts[0])
            ? null
            : trim($indexParts[0]);

        $suggestion = empty($indexParts[1])
            ? $ucl->findStageDef($cloner)->getDescription()
            : trim($indexParts[1]);

        return [$index, $suggestion];
    }

    protected function separateRouteAndIndex(string $content) : array
    {
        $content = trim($content);
        $parts = preg_split('/\s+/', $content, 2);
        $first = trim($parts[0]);
        $index = $parts[1] ?? '';

        return [$first, $index];
    }
}