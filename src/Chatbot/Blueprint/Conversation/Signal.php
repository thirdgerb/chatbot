<?php


namespace Commune\Chatbot\Blueprint\Conversation;


use Commune\Chatbot\Framework\Events\ChatbotPipeEvent;

/**
 * @deprecated
 * @property-read Conversation $conversation
 */
interface Signal
{
    public function withConversation(Conversation $conversation) : Signal;

    public function handle(ChatbotPipeEvent $event) : void;
}