<?php


namespace Commune\Chatbot\Blueprint\Conversation;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;

/**
 * 将回复消息(replyMsg)渲染成真正发送消息的模板.
 * 绑定到进程级容器上, 允许依赖注入, 不过注入的只能是进程级的服务.
 * 由于传入了请求级容器 conversation, 可以通过它获取请求级的服务.
 * 
 * template that rendering reply message to real messages
 *
 * bound to process container.
 * So dependencies should only be process container's bindings
 *
 * will get conversation container by render method
 *
 */
interface ReplyTemplate
{
    /**
     * 将 ReplyMsg 渲染成 Message[] 数组.
     *
     * @param ReplyMsg $reply
     * @param Conversation $conversation
     * @return Message[]
     */
    public function render(ReplyMsg $reply, Conversation $conversation) : array;

}