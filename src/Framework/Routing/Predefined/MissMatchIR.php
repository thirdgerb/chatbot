<?php

/**
 * Class MissMatchIR
 * @package Commune\Chatbot\Host\Routing\Predefined
 */

namespace Commune\Chatbot\Framework\Routing\Predefined;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Routing\IntentRoute;
use Commune\Chatbot\Framework\Routing\Router;
use Commune\Chatbot\Framework\Intent\Intent;

class MissMatchIR extends IntentRoute
{

    public function __construct(ChatbotApp $app, Router $router)
    {
        parent::__construct($app, $router, null);

        $message = $app->getMissMatchMessage();

        $this->action(function(Context $context, Intent $intent) use ($message){
            $context->info($message);
        })->backward();
    }

}