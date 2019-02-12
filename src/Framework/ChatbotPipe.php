<?php

/**
 * Class Pipe
 * @package Commune\Chatbot\Server
 */

namespace Commune\Chatbot\Framework;


use Commune\Chatbot\Framework\Conversation\Conversation;

interface ChatbotPipe
{

    public function handle(Conversation $conversation, \Closure $next) : Conversation;

}