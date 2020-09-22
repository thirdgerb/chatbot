<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindDef;

use Commune\Support\Struct\AStruct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string|null $onCancel
 * 当语境 cancel 时, 默认跳转的 stage
 *
 * @property string|null $onQuit
 * 当语境 quit 时, 默认跳转的 stage
 *
 * @property string[]|null $heedFallbackStrategies
 * 当无法理解语义时, 回调的策略. 为 [] 表示不回调, 为 null 表示使用全局回调策略.
 *
 * @property string[] $stageRoutes
 * 当前 Context 的通用 stage 路由, 意图命中了路由中的 stage name, 都会跳转. 可以用 "*" 作为通配符
 *
 * @property string[] $contextRoutes
 * 当前 Context 的通用 context 路由, 意图命中了路由中的 context, 都会跳转. 可以用 "*" 作为通配符
 *
 * @property string[]|null $comprehendPipes
 * 当前 Context 使用的语义理解中间件, 为 null 表示使用全局默认的中间件. [] 表示不用中间件.
 * 每一个中间件都应该是 Commune\Blueprint\NLU\NLUService 的类名或者 interface 名.
 * @see \Commune\Blueprint\NLU\NLUService
 *
 * @property string[] $auth
 * 当前 Context 的访问权限, @see \Commune\Blueprint\Framework\Auth\Policy
 *
 * @property string $commandMark
 * 当前 Context 如果允许命令的话, 命令必要的前缀. 默认是 "!", 例如 "!help"
 *
 * @property string[] $commands
 * 当前 Context 允许的命令类名, 每个命令都应该是上下文相关的命令
 * @see \Commune\Ghost\Context\Command\AContextCmdDef
 */
class ContextStrategyOption extends AStruct
{
    public static function stub(): array
    {
        return [
            'auth' => [],
            'onCancel' => null,
            'onQuit' => null,
            'heedFallbackStrategies' => null,
            'comprehendPipes' => null,
            'stageRoutes' => [],
            'contextRoutes' => [],
            'commandMark' => '!',
            'commands' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}