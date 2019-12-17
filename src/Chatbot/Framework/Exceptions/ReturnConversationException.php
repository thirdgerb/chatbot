<?php

/**
 * Class MessageInterfaceException
 * @package Commune\Chatbot\Framework\Exceptions
 */

namespace Commune\Chatbot\Framework\Exceptions;

use Commune\Chatbot\Blueprint\Conversation\Conversation;

/**
 * 中断, 直接返回conversation
 * 是一种工具类的 Exception.
 */
class ReturnConversationException extends \RuntimeException
{

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * ConversationalException constructor.
     * @param Conversation $conversation
     */
    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
        parent::__construct('conversational exception', null);
    }

    /**
     * @return Conversation
     */
    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

}