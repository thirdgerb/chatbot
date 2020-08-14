<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Analysers;

use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Components\Markdown\Mindset\SectionStageDef;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface BranchAnalyser
{

    /**
     * @param string $content
     * @param SectionStageDef $def
     * @return StageDef|SectionStageDef
     */
    public function __invoke(
        string $content,
        SectionStageDef $def
    ) : StageDef;

}