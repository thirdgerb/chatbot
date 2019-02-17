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
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Routing\Runners\Action;

class PreparedIR extends IntentRoute
{
    protected $action;

    public function __construct(ChatbotApp $app, Router $router)
    {
        parent::__construct($app, $router, 'prepared');

        $this->action = new Action($this->app);
        $this->action
            ->call(function(Context $context, Intent $intent){
                return $context->callConfigMethod('prepared', $intent);
            });
    }

    public function action($condition = null): Action
    {
        return $this->action;
    }

}