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

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\Context\Prototypes\AbsIntentContext;
use Commune\Ghost\Support\ContextUtils;


/**
 * 事件类语境. 通常用于把某个意图当成一个事件来响应.
 * 例如 cancel, quit 等.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AIntentContext extends AbsIntentContext
{

    public static function __name(): string
    {
        return ContextUtils::normalizeContextName(static::class);
    }

    /**
     * 定义响应事件.
     *
     * @param Dialog $prev
     * @return Operator|null
     */
    abstract public static function __redirect(Dialog $prev): ? Operator;



}