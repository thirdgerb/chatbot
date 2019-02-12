<?php

/**
 * Class FallbackIR
 * @package Commune\Chatbot\Host\Routing\Predefined
 */

namespace Commune\Chatbot\Framework\Routing\Predefined;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Routing\IntentRoute;
use Commune\Chatbot\Framework\Routing\Router;
use Commune\Chatbot\Framework\Intent\IntentFactory;

class FallbackIR extends IntentRoute
{

    public function __construct(ChatbotApp $app, Router $router, string $id, IntentFactory $matcher = null)
    {
        $id .= ':fallback';
        parent::__construct($app, $router, $id, $matcher);
    }

}