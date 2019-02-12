<?php

/**
 * Class Bootstrapper
 * @package Commune\Chatbot\Framework\Bootstrap
 */

namespace Commune\Chatbot\Contracts\Bootstrap;


use Commune\Chatbot\Contracts\ChatbotApp;

interface Bootstrapper
{
    public function bootstrap(ChatbotApp $app);

}