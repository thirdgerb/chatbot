<?php

/**
 * Class IntentCfg
 * @package Commune\Chatbot\Framework\Intent
 */

namespace Commune\Chatbot\Framework\Intent;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Routing\IntentRoute;
use Commune\Chatbot\Framework\Routing\Router;

abstract class IntentCfg
{
    const SIGNATURE = '';

    const DESCRIPTION = '';

    const ALIAS = [
    ];

    const REGEX = [
    ];

    const EXAMPLES = [];

    final public function __construct()
    {
    }

    final public function getIntentName() : string
    {
        return static::class;
    }

    public function defaultRoute(ChatbotApp $app, Router $router) : ? IntentRoute
    {
        return null;
    }

    final public function getSignature() : string
    {
        return static::SIGNATURE;
    }

    final public function getDescription() : string
    {
        return static::DESCRIPTION;
    }

}