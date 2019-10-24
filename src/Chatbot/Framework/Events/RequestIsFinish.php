<?php


namespace Commune\Chatbot\Framework\Events;


use Commune\Chatbot\Blueprint\Conversation\Conversation;

class RequestIsFinish
{
    /**
     * @var Conversation
     */
    public $conversation;

    /**
     * ConversationFinish constructor.
     * @param Conversation $conversation
     */
    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }


}