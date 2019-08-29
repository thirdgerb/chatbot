<?php


namespace Commune\Chatbot\Blueprint\Conversation;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;

/**
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
     * @param ReplyMsg $reply
     * @param Conversation $conversation
     * @return Message[]
     */
    public function render(ReplyMsg $reply, Conversation $conversation) : array;

}