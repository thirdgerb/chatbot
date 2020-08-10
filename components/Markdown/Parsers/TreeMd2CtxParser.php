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
class TreeMd2CtxParser implements MD2ContextParser
{
    public function parse(
        MDGroupOption $group,
        MDParser $parser
    ): ContextDef
    {
        // TODO: Implement parse() method.
    }


}