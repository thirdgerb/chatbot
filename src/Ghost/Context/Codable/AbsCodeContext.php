<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context\Codable;

use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Ghost\Context\Prototype\IContext;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AbsCodeContext extends IContext implements CodeContext
{

    public static function getContextName() : string
    {
        return ContextUtils::normalizeContextName(static::class);
    }

    public static function makeDef(): ContextDef
    {
        // TODO: Implement makeDef() method.
    }



}