<?php

namespace Commune\Chatbot\Blueprint\Conversation;

use Commune\Chatbot\Blueprint\Message\Message;

/**
 * 使用IoC容器来承载一个请求, 将请求内的依赖都包装到这个容器内.
 * 好处是让一个请求内不同的模块, 可以通过依赖注入的方式分享各种组件.
 */
interface Conversation extends ConversationContainer, RunningSpy
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

    /*------------ conversational ------------*/

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
     * 获取输入消息的封装对象.
     * @return IncomingMessage
     */
    public function getIncomingMessage() : IncomingMessage;

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
     * @return NLU
     */
    public function getNLU() : NLU;

    /**
     * 和用户对话的模块.
     * @return Speech
     */
    public function getSpeech() : Speech;

    /**
     * 回复消息给当前用户
     *
     * @param Message $message
     * @param bool $immediately
     */
    public function reply(Message $message, bool $immediately = false) : void;


    /**
     * 渲染消息, 如果是 reply message, 会使用 renderer 进行渲染.
     *
     * @see \Commune\Chatbot\Blueprint\Message\ReplyMsg
     * @see Renderer
     *
     * render reply placeholder to real reply messages
     *
     * @param Message $reply
     * @return Message[]
     */
    public function render(Message $reply) : array;

    /**
     * 转发消息不是给当前用户, 而是给指定的用户.
     *
     * deliver message to certain user
     *
     * @param string $userId
     * @param Message $message
     * @param bool $immediately
     * @param string|null $chatId  not generate chatId with userId but set it
     */
    public function deliver(
        string $userId,
        Message $message,
        bool $immediately = false,
        string $chatId = null
    ) : void;

    /*------------ conversation messages ------------*/

    /**
     * 保存要发送的消息.
     *
     * @param MessageRequest $request
     * @param ConversationMessage $message
     * @param bool $immediatelyBuffer  是否立刻交给 request 去 buffer, 如果这么做就反悔不了了.
     */
    public function saveConversationReply(
        MessageRequest $request,
        ConversationMessage $message,
        bool  $immediatelyBuffer
    ) : void;


    /**
     * 获取已有的回复消息.
     * @return ConversationMessage[]
     */
    public function getReplies() : array;

    /**
     * 计算回复消息的数量.
     * @return int
     */
    public function countReplies() : int;

    /**
     * 是否有向用户提出问题, 从而构成对话.
     * @return bool
     */
    public function hasAsked() : bool;

    /**
     * 清空所有要发送的消息
     */
    public function flushConversationReplies() : void;


    /**
     * 完成一个请求, 把所有消息发送出去.
     * 要调用 MessageRequest 的 flushChatMessage 与 finishRequest
     */
    public function finishRequest() : void;

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