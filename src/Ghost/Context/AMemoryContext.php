<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context;

use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Ghost\Context\Prototypes\AbsMemoryContext;
use Commune\Ghost\Support\ContextUtils;


/**
 * 作为记忆体存在的 context.
 * 可以用多轮对话完成某个记忆单元.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title 记忆体的标题,
 * @desc 记忆体的简介
 */
abstract class AMemoryContext extends AbsMemoryContext
{

    /**
     * 上下文记忆体的 contextName. 修改后可以自己定义.
     * @return string
     */
    public static function __name() : string
    {
        return ContextUtils::normalizeContextName(static::class);
    }

    /**
     * 上下文记忆体的长程作用域.
     * @see ClonerScope
     * @return string[]
     */
    abstract public static function __scopes(): array;

    /**
     * 上下文记忆体的默认值.
     * @return array
     */
    abstract public static function __defaults(): array;

}