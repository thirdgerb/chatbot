<?php

/**
 * Class Matcher
 * @package Commune\Chatbot\Framework\Intent\Matchers
 */

namespace Commune\Chatbot\Framework\Intent\Matchers;


use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Intent\Intent;

interface Matcher
{

    public function match(Conversation $conversation) : ? Intent;

}