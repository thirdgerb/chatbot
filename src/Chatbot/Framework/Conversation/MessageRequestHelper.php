<?php


namespace Commune\Chatbot\Framework\Conversation;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\ConversationLogger;

trait MessageRequestHelper
{

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * @var ConversationLogger
     */
    protected $logger;

    public function withConversation(Conversation $conversation) : void
    {
        $this->conversation = $conversation;
        $this->logger = $conversation->getLogger();
    }

    public function finishRequest() : void
    {
        $this->conversation = null;
        $this->logger = null;
    }

}