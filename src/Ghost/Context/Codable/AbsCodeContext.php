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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindMeta\ContextMeta;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Context\Prototype\IContext;
use Commune\Ghost\Support\ContextUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsCodeContext extends IContext implements CodeContext
{

    public static function getContextName() : string
    {
        return ContextUtils::normalizeContextName(static::class);
    }

    public static function wrapContext(Cloner $cloner, Ucl $ucl): Context
    {
        return new static($ucl, $cloner);
    }

    public static function makeDef(ContextMeta $meta): ContextDef
    {
        return new ICodeContextDef(static::class, $meta);
    }


}