<?php

/**
 * Class RuntimeLogger
 * @package Commune\Chatbot\Framework
 */

namespace Commune\Chatbot\Framework;


use Commune\Chatbot\Framework\Conversation\Conversation;

class RuntimeLogger
{

    public function next(callable  $next, Conversation $conversation) : Conversation
    {
        $result = $next($conversation);
    }

}