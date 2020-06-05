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
use Commune\Ghost\Context\IContext;
use Commune\Support\Option\Meta;
use Commune\Support\Option\Wrapper;
use Commune\Blueprint\Ghost\Context\CodeContext;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsCodeContext extends IContext implements CodeContext
{
    public static function makeUcl(array $query = [], string $stage = ''): Ucl
    {
        $name = static::__name();
        return Ucl::make($name, $query, $stage);
    }


    public function toMeta(): Meta
    {
        return $this->_def->toMeta();
    }

    public static function wrapMeta(Meta $meta): Wrapper
    {
        return static::__def($meta);
    }

    public static function __def(ContextMeta $meta = null): ContextDef
    {
        return new ICodeContextDef(static::class, $meta);
    }

    public static function create(Cloner $cloner, Ucl $ucl): Context
    {
        return new static($cloner, $ucl);
    }

    public static function selfRegisterToMind(Ghost\Mindset $mind, bool $force = false): void
    {
        $def = static::__def();
        $mind->contextReg()->registerDef($def, !$force);
    }



}