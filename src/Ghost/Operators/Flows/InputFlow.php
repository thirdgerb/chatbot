<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Flows;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Ghost\Operators\FlowOperator;
use Commune\Ghost\Operators\OnInput;

/**
 * 处理输入信息的流程.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class InputFlow extends FlowOperator
{
    /**
     * 启动的完整流程
     * @var string[]
     */
    protected $domino = [
        // 消息是不是异步来的 Yield 消息
        OnInput\YieldCheck::class,
        // 消息是不是异步来的 Retain
        OnInput\RetainCheck::class,
        // 尝试理解输入消息, 获得更多抽象信息
        OnInput\ComprehendPipes::class,
        // 如果是新的 Process, 则走激活流程.
        OnInput\BrandNewSession::class,
        // 检查是否有 Watch Context 在拦截请求
        OnInput\WatchCheck::class,
        // 检查是否重定向到其它 Route
        OnInput\StageRouting::class,
        OnInput\ContextRouting::class,
        OnInput\TryHeed::class,
    ];

    protected function doInvoke(Cloner $cloner): ? Operator
    {
        return null;
    }
}