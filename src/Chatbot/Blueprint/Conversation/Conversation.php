<?php

/**
 * Class Conversation
 * @package Commune\Chatbot\Blueprint\Conversation
 */

namespace Commune\Chatbot\Blueprint\Conversation;

use Commune\Chatbot\Blueprint\Message\Message;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * 使用IoC容器来承载一个请求, 将请求内的依赖都包装到这个容器内.
 * 好处是让一个请求内不同的模块, 可以通过依赖注入的方式分享各种组件.
 *
 *
 */
interface Conversation extends ConversationContainer
{

    /*------------ create ------------*/

    /**
     * @return bool
     */
    public function isInstanced() : bool;

    /**
     * @return string[]
     */
    public static function getInstanceIds() : array;

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

    /*------------ conversational ------------*/

    /**
     * conversation 的 trace id, 用于记录各种数据.
     * @return string
     */
    public function getTraceId() : string;

    /**
     * 所有的conversation 都要有独立的id
     * @return string
     */
    public function getConversationId() : string;

    public function getIncomingMessage() : IncomingMessage;

    /**
     * 获取用户
     * @return User
     */
    public function getUser() : User;

    public function getChat() : Chat;

    /*------------ components ------------*/

    /**
     * 获取日志模块
     * @return ConversationLogger
     */
    public function getLogger() : ConversationLogger;

    /**
     * 当前请求的处理器
     * @return MessageRequest
     */
    public function getRequest() : MessageRequest;

    /**
     * 和用户对话的模块.
     * @return Monologue
     */
    public function monolog() : Monologue;

    /**
     * 回复消息的接口.
     * @param Message $message
     */
    public function reply(Message $message) : void;

    /**
     * 需要发送出去的信息.
     * @return ConversationMessage[]
     */
    public function getOutgoingMessages() : array;

    /*------------ event ------------*/

    /**
     * 触发事件.
     * @param Event $event
     */
    public function fire(Event $event) : void;

    /*------------ input & output ------------*/

    /**
     * 结束一个 conversation
     */
    public function finish() : void;

    /**
     * 在结束的时候触发的逻辑.
     * @param callable $caller
     * @param bool $atEndOfTheQueue
     */
    public function onFinish(callable $caller, bool $atEndOfTheQueue = true) : void;



}