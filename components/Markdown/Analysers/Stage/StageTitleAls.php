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
use Commune\Components\Markdown\Analysers\BranchAnalyser;
use Commune\Components\Markdown\Mindset\SectionStageDef;


/**
 * [title]: title
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StageTitleAls implements BranchAnalyser
{
    public static function getCommentID(): string
    {
        return 'branch.title';
    }

    public function __invoke(
        string $content,
        SectionStageDef $def
    ) : StageDef
    {
        $def->title = $content;
        return $def;
    }


}