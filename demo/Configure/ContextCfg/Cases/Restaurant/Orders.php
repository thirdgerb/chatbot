<?php

/**
 * Class Orders
 * @package Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Restaurant
 */

namespace Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Restaurant;


use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Routing\DialogRoute;

class Orders extends ContextCfg
{
    const SCOPE = [Scope::SESSION];

    const DATA = [
        'selected' => [
            // 'name' => selected,
        ],
    ];

    public function routing(DialogRoute $route)
    {
        $route->prepared()
            ->redirect()
                ->intended();

    }


}