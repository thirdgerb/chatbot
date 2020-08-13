<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Analysers\Stage;

use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Components\Markdown\Analysers\BranchAnalyser;
use Commune\Components\Markdown\Mindset\SectionStageDef;
use Commune\Components\Tree\Prototype\BranchStageDef;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StageDescAls implements BranchAnalyser
{
    public static function getCommentID(): string
    {
        return 'branch.desc';
    }

    public function __invoke(
        string $content,
        SectionStageDef $def
    ): StageDef
    {
        $def->desc = $content;
        return $def;
    }


}