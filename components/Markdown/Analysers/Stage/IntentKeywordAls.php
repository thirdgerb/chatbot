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
use Commune\Components\Markdown\Analysers\StageAnalyser;
use Commune\Components\Markdown\Mindset\SectionStageDef;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IntentKeywordAls implements StageAnalyser
{
    public function __invoke(
        string $content,
        SectionStageDef $def
    ): StageDef
    {
        $words = explode(',', $content);
        $words = array_map('trim', $words);
        $words = array_filter($words, function($word){
            return empty($word);
        });

        if (empty($words)) {
            return $def;
        }

        $intentDef = $def->asIntent->toWrapper();
        $intentDef->appendKeywords(...$words);
        $def->asIntent = $intentDef->toMeta();
        return $def;
    }


}