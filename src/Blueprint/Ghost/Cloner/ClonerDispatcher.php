<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Cloner;

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Protocols\HostMsg\Convo\ContextMsg;

/**
 * 管理各种异步事务机制的模块.
 * 将复杂的异步事务机制进行统一的高级封装, 方便查看.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ClonerDispatcher
{

    /**
     * 基于 asyncJob 的原理,
     * 异步创建子进程调用一个 Service,
     * 将结果返回给当前 Session.
     *
     * @param string $service
     * 对话式的服务 @see DialogicService
     *
     * @param array $params
     *
     * @param string $method  服务调用的方法.
     */
    public function asyncService(string $service, array $params, string $method = '__invoke') : void;


    /**
     * 使用一个对话作为一个异步任务.
     * 会创建独立的 conversationId, 交给子进程去解决.
     *
     * 除非客户端指定 conversationId, 否则客户端无法和子进程通话.
     * 然而子进程发放的所有消息会广播给整个 session.
     *
     * 因此这样的任务最好都是一次性的, 执行完了返回一个结果.
     *
     * @param Ucl $job
     */
    public function asyncJob(Ucl $job) : void;

    /**
     * 将一个 context 投递到另一个会话中.
     * 这是一个更底层的方法.
     * 通过这个方法可以把上下文像踢皮球一样, 在多个对话中传递.
     *
     * @param string $sessionId
     * @param string $convoId
     * @param Context $context
     * @param int $mode
     */
    public function yieldContext(
        string $sessionId,
        Context $context,
        int $mode = ContextMsg::MODE_BLOCKING,
        string $convoId = ''
    ) : void;
}