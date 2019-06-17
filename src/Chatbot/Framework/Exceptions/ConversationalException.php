<?php

/**
 * Class MessageInterfaceException
 * @package Commune\Chatbot\Framework\Exceptions
 */

namespace Commune\Chatbot\Framework\Exceptions;

use Commune\Chatbot\Blueprint\Conversation\Conversation;

/**
 * 中断, 直接返回conversation
 *
 * Interface MessageInterfaceException
 * @package Commune\Chatbot\Framework\Exceptions
 */
class ConversationalException extends LogicException
{

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * ConversationalException constructor.
     * @param Conversation $conversation
     * @param \Throwable $e
     */
    public function __construct(Conversation $conversation, \Throwable $e = null)
    {
        $this->conversation = $conversation;
        parent::__construct('conversational exception', $e);
    }

    /**
     * @return Conversation
     */
    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

}