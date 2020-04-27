<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost;

use Commune\Ghost\Memory\AMemory;

/**
 * 默认的记忆容器.
 * 可以继承本容器, 快速定义记忆体.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMemory extends AMemory
{
    const NAME = 'session';

    const SCOPES = [
        // 定义长程的记忆体作用域维度.
        // ClonerScope::CLONE_ID,
        // ClonerScope::MONTH
    ];


    public static function getScopes(): array
    {
        return static::SCOPES;
    }

    public static function stub(): array
    {
        return [];
    }

    public static function getMemoryName(): string
    {
        return 'memory.' . static::NAME;
    }
}