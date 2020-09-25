<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Context;

use Commune\Blueprint\Ghost\MindDef\ContextStrategyOption;
use Commune\Support\Option\AbsOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string[] $auth
 * @property-read int $priority                     语境的默认优先级.
 *
 * @property-read string[] $queryNames              语境的参数定义, Ucl 必须携带参数, 否则调用失败.
 *
 * @property-read string[] $memoryScopes
 * @property-read array $memoryAttrs
 *
 * @property-read ContextStrategyOption $strategy
 *
 */
class CodeContextOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            // context 的优先级. 若干个语境在 blocking 状态中, 根据优先级决定谁先恢复.
            'priority' => 0,

            // context 的默认参数名, 类似 url 的 query 参数.
            // query 参数值默认是字符串.
            // query 参数如果是数组, 则定义参数名时应该用 [] 做后缀, 例如 ['key1', 'key2', 'key3[]']
            'queryNames' => [],


            // 定义 context 上下文记忆的作用域.
            // 相关作用域参数, 会自动添加到 query 参数中.
            // 作用域为空, 则是一个 session 级别的短程记忆.
            // 不为空, 则是长程记忆, 会持久化保存.
            'memoryScopes' => [],

            // memory 记忆体的默认值.
            'memoryAttrs' => [],

            'strategy' => [
                'auth' => [],
                'onCancel' => null,
                'onQuit' => null,
                'heedFallbackStrategies' => null,
                'comprehendPipes' => null,
                'stageRoutes' => [],
                'contextRoutes' => [],
                'commandMark' => '!',
                'commands' => [],
            ],
        ];
    }

    public static function relations(): array
    {
        return [
            'strategy' => ContextStrategyOption::class,
        ];
    }


}