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
use Commune\Ghost\Support\ContextUtils;


/**
 * 将 markdown 注解中的
 *
 * [name]: stage name
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StageNameAls implements StageAnalyser
{
    public function __invoke(
        string $content,
        SectionStageDef $def
    ): StageDef
    {
        $def->stageName = $name = trim($content);
        $def->name = ContextUtils::makeFullStageName(
            $def->contextName,
            $name
        );

        return $def;
    }


}