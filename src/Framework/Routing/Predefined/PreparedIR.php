<?php

/**
 * Class PreparedIR
 * @package Commune\Chatbot\Host\Routing\Predefined
 */

namespace Commune\Chatbot\Framework\Routing\Predefined;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Routing\IntentRoute;
use Commune\Chatbot\Framework\Routing\Router;
use Commune\Chatbot\Framework\Intent\IntentData;
use Commune\Chatbot\Framework\Intent\IntentFactory;

class PreparedIR extends IntentRoute
{
    public function __construct(ChatbotApp $app, Router $router, string $id = null, IntentFactory $matcher = null)
    {
        $id .= ':prepared';
        parent::__construct($app, $router, $id, $matcher);

        $this->action(function(Context $context, IntentData $intent){
            $context->fireEvent('prepared');
        });
    }

}