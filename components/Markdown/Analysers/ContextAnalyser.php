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

use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Components\Markdown\Mindset\MDContextDef;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ContextAnalyser
{

    /**
     * @param string $content
     * @param MDContextDef $def
     * @return ContextDef
     */
    public function __invoke(
        string $content,
        MDContextDef $def
    ) : ContextDef;

}