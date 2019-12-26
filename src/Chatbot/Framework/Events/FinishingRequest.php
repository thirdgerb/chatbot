<?php

namespace Commune\Chatbot\Framework\Events;

use Commune\Chatbot\Blueprint\Conversation\Conversation;

class FinishingRequest
{
    /**
     * @var Conversation
     */
    public $conversation;

    /**
     * FinishingRequest constructor.
     * @param Conversation $conversation
     */
    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }


}