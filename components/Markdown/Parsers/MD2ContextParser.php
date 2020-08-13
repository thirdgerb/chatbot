<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Parsers;

use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Components\Markdown\Options\MDGroupOption;
use Commune\Support\Markdown\Parser\MDParser;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface MD2ContextParser
{

    /**
     * @param MDGroupOption $group
     * @param MDParser $parser
     * @return ContextDef
     */
    public function parse(
        MDGroupOption $group,
        MDParser $parser
    ) : ContextDef;

}