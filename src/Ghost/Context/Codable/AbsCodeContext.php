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

use Commune\Blueprint\Ghost;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Context\Prototype\IContext;
use Commune\Ghost\Support\ContextUtils;
use Commune\Support\Option\Meta;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsCodeContext extends IContext implements CodeContext
{

    public static function __name() : string
    {
        return ContextUtils::normalizeContextName(static::class);
    }

    public function getMeta(): Meta
    {
        return $this->_def->getMeta();
    }

    public static function __def(ContextMeta $meta = null): ContextDef
    {
        return new ICodeContextDef(static::class, $meta);
    }

    public static function wrap(Cloner $cloner, Ucl $ucl): Context
    {
        return new static($cloner, $ucl);
    }

    public static function selfRegisterToMind(Ghost\Mindset $mind): void
    {
        $def = static::__def(static::class);
        $mind->contextReg()->registerDef($def, true);
    }


}