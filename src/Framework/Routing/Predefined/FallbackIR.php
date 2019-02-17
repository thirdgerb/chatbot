<?php

/**
 * Class FallbackIR
 * @package Commune\Chatbot\Host\Routing\Predefined
 */

namespace Commune\Chatbot\Framework\Routing\Predefined;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Routing\IntentRoute;
use Commune\Chatbot\Framework\Routing\Router;

class FallbackIR extends IntentRoute
{

    public function __construct(ChatbotApp $app, Router $router)
    {
        parent::__construct($app, $router, 'fallback');
    }

}