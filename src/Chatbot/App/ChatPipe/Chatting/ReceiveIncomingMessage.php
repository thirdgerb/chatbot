<?php

namespace Commune\Chatbot\App\ChatPipe\Chatting;


use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Symfony\Component\EventDispatcher\Event;

class ReceiveIncomingMessage extends Event
{
    /**
     * @var ConversationMessage
     */
    protected $incomingMessage;

    /**
     * @var bool
     */
    protected $sending;

    /**
     * ReceiveIncomingMessage constructor.
     * @param ConversationMessage $incomingMessage
     * @param bool $sending
     */
    public function __construct(ConversationMessage $incomingMessage, bool $sending)
    {
        $this->incomingMessage = $incomingMessage;
        $this->sending = $sending;
    }


    /**
     * @return ConversationMessage
     */
    public function getIncomingMessage(): ConversationMessage
    {
        return $this->incomingMessage;
    }

    /**
     * @return bool
     */
    public function isSending(): bool
    {
        return $this->sending;
    }

}