<?php

/**
 * Class ConversationException
 * @package Commune\Chatbot\Framework\Exceptions
 */

namespace Commune\Chatbot\Framework\Exceptions;


use Commune\Chatbot\Framework\Conversation\Conversation;

class ConversationException extends ChatbotException
{
    protected $conversation;

    public function __construct(Conversation $conversation, int $code = 0)
    {
        $this->conversation = $conversation;
        parent::__construct('', $code, null);
    }

    /**
     * @return Conversation
     */
    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

}