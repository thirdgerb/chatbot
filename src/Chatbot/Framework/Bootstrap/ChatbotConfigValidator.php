<?php


namespace Commune\Chatbot\Framework\Bootstrap;


use Commune\Chatbot\Blueprint\Application;

class ChatbotConfigValidator implements Bootstrapper
{
    public function bootstrap(Application $app): void
    {
        $app->getConfig();
    }


}