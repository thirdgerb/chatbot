<?php


namespace Commune\Chatbot\Blueprint\Pipeline;


use Commune\Chatbot\Contracts\ChatServer;

interface InitialPipe extends ChatbotPipe
{

    public function getServer() : ChatServer;

}