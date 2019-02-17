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
use Commune\Chatbot\Framework\Routing\Runners\Action;

class MissMatchIR extends IntentRoute
{

    public function __construct(ChatbotApp $app, Router $router)
    {
        parent::__construct($app, $router, 'miss_match');

        $message = $app->getConfig(ChatbotApp::MESSAGES_MISS_MATCH, 'miss match');

        $this->action = new Action($app);
        $this->action
            ->call(function(Context $context, Intent $intent) use ($message){
                $context->info($message);
            })->redirect()
                ->backward();
    }

    public function action($condition = null): Action
    {
        return $this->action;
    }

}