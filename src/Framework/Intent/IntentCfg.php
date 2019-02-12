<?php

/**
 * Class IntentCfg
 * @package Commune\Chatbot\Configure
 */

namespace Commune\Chatbot\Framework\Intent;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Routing\IntentRoute;
use Commune\Chatbot\Framework\Routing\Router;

class IntentCfg
{
    const ENTITIES = [];

    const DESCRIPTION = '';

    const SIGNATURE = '';

    const REGEX = [
        // regex,
        // entityKey1,
        // entityKey2,
    ];

    const EXAMPLES = [];

    final public function __construct()
    {
    }

    public function defaultRoute(ChatbotApp $app, Router $router) : IntentRoute
    {
        return $app->getIntentDefaultRoute($router);
    }


}