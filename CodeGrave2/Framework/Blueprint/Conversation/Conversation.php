<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Conversation;

use Commune\Framework\Blueprint\Chat;
use Commune\Framework\Blueprint\Conversation\IncomingMessage;
use Commune\Framework\Blueprint\Container;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Platform\Request;
use Commune\Shell\Platform\Response;
use Commune\Shell\Platform\Server;

/**
 * 单轮会话的容器. 可以理解成多轮对话的一个状态机.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 属性:
 *
 * @property-read string $traceId
 * @property-read string $messageId
 *
 *
 * 以下所有组件都可以在调用方法时依赖注入
 *
 * @property-read Request $request 当前请求
 * @property-read Response $response 当前响应
 * @property-read IncomingMessage $incomingMessage 输入消息
 * @property-read Server $server  服务端实例
 * @property-read Shell $shell  Shell 实例
 * @property-read Comprehension $comprehension 理解单元.
 * @property-read Logger $logger 对话级的日志
 * @property-read Chat $chat 机器人所识别的对话通道. 每个 Chat 对应机器人的一个分身.
 * @property-read Scope $scope 对话的维度.
 * @property-read Messenger $messenger 消息发送组件.
 *
 */
interface Conversation extends Container
{

    /*------------ create ------------*/

    /**
     * Conversation 容器有一个进程级单例, 用于注册服务.
     * 每一个请求会重新实例化一次. 判断当前容器是不是在请求中实例化的, 可以用这个方法.
     *
     * ether conversation container is instanced by request (true)
     * or initialized by chat app to register bindings (false)
     *
     * @return bool
     */
    public function isInstanced() : bool;

    /*------------ policy ------------*/

    /**
     * 检查当前conversation 是否拥有某种权限.
     * 需要传入一个class name

     * @param string $policyName
     * @see Policy
     *
     * @return bool
     */
    public function allow(string $policyName) : bool;


}