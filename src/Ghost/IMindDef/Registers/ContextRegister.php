<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IMindDef\Registers;

use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindSelfRegister;
use Commune\Blueprint\Ghost\Mindset;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ContextRegister implements MindSelfRegister
{
    abstract public static function makeDef() : ContextDef;

    public static function selfRegisterToMind(Mindset $mindset, bool $force = false): void
    {
        $def = static::makeDef();
        $mindset->contextReg()->registerDef($def, $force);
    }


}
