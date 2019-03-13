<?php

/**
 * Class Menu
 * @package Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Restaurant
 */

namespace Commune\Chatbot\Demo\Configure\ContextCfg\Cases\Restaurant;


use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Routing\DialogRoute;

class Menu extends ContextCfg
{
    const SCOPE = [Scope::MESSAGE];

    public function routing(DialogRoute $route)
    {

    }


}