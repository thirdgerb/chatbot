<?php

namespace Commune\Chatbot\App\ChatPipe\Chatting;

use Commune\Chatbot\Blueprint\Conversation\ConversationMessage;
use Symfony\Component\EventDispatcher\Event;

class SendingOutgoingMessage extends Event
{

    /**
     * @var ConversationMessage[]
     */
    protected $outgoingMessages;

    /**
     * SendingOutgoingMessage constructor.
     * @param ConversationMessage[] $outgoingMessages
     */
    public function __construct(array $outgoingMessages)
    {
        $this->outgoingMessages = $outgoingMessages;
    }

    /**
     * @return ConversationMessage[]
     */
    public function getOutgoingMessages(): array
    {
        return $this->outgoingMessages;
    }
}