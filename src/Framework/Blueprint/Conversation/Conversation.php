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

use Commune\Framework\Blueprint\Chat\Chat;
use Commune\Framework\Blueprint\Chat\IncomingMessage;
use Commune\Framework\Blueprint\Container;
use Commune\Platform\Request;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
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
     *
     * 条件1: 参数是 class, 而且是 Ability 的子类.
     * 条件2: Ability 可以被容器实例化.
     * 条件3: 运行 isAllowing($conversation) 通过.
     *
     * @param string $abilityInterfaceName
     * @return bool
     */
    public function isAbleTo(string $abilityInterfaceName) : bool;

    /*------------ scope ------------*/

    /**
     * conversation 的 trace id, 来自于 MessageRequest
     * 用于记录调用链, 方便排查问题.
     *
     * @return string
     */
    public function getTraceId() : string;

    /**
     * 所有的conversation 都要有独立的id
     * @return string
     */
    public function getConversationId() : string;

    /**
     * 获取用户信息
     * @return User
     */
    public function getUser() : User;

    /**
     * 获取 Chat (会话) 信息.
     * @return Chat
     */
    public function getChat() : Chat;

    /*------------ incomingMessage ------------*/


    /**
     * 获取输入消息的封装对象.
     *
     * @param IncomingMessage $message
     */
    public function setIncomingMessage(IncomingMessage $message) : void ;


    /**
     * 获取输入消息的封装对象.
     * @return IncomingMessage
     */
    public function getIncomingMessage() : IncomingMessage;


    /*------------ components ------------*/

    /**
     * 获取日志模块
     * @return ConversationLogger
     */
    public function getLogger() : ConversationLogger;

    /**
     * 当前请求的处理器
     * @return Request
     */
    public function getRequest() : Request;

    /**
     *
     * @return NLU
     */
    public function getNLU() : NLU;

    /*------------ event ------------*/

    /**
     * 触发事件.
     * @param \object $event
     */
    public function fire(object $event) : void;

    /*------------ input & output ------------*/

    /**
     * 结束一个 conversation
     * 为 destruct 做准备.
     */
    public function finish() : void;

    /**
     * 在结束的时候触发的逻辑.
     * @param callable $caller
     * @param bool $atEndOfTheQueue
     */
    public function onFinish(callable $caller, bool $atEndOfTheQueue = true) : void;



}