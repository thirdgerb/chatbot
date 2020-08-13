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
use Commune\Ghost\IMindDef\IIntentDef;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IntentSpellAls implements BranchAnalyser
{
    public static function getCommentID(): string
    {
        return 'branch.int.spell';
    }

    public function __invoke(
        string $content,
        SectionStageDef $def
    ): StageDef
    {
        $wrapper = $def->asIntent->toWrapper();
        if ($wrapper instanceof IIntentDef) {
            $wrapper->spell = $content;
            $def->asIntent = $wrapper->toMeta();
        }

        return $def;
    }


}