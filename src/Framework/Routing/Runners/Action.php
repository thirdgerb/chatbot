<?php

/**
 * Class Action
 * @package Commune\Chatbot\Framework\Routing\Runners
 */

namespace Commune\Chatbot\Framework\Routing\Runners;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Directing\Director;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Message\Message;
use Commune\Chatbot\Framework\Routing\IntentRoute;

class Action extends Runner
{
    protected $actions = [];

    /**
     * @var Action
     */
    protected $next;

    /**
     * @var callable
     */
    protected $condition;

    /**
     * @var Redirect
     */
    protected $redirect;

    /**
     * @var ChatbotApp
     */
    protected $app;

    public function __construct(ChatbotApp $app, $condition = null)
    {
        $this->app = $app;
        $this->condition = IntentRoute::wrapCondition($condition);
    }

    /*----------- to -----------*/

    public function action($condition = null) : Action
    {
        $this->next = new Action($this->app, $condition);
        return $this->next;
    }

    public function redirect($condition = null) : Redirect
    {
        $this->redirect = new Redirect($this, $condition);
        return $this->redirect;
    }

    /*----------- run -----------*/

    public function run(Director $director, Context $context, Intent $intent): Conversation
    {
        $right = !isset($this->condition) || call_user_func($this->condition, $context);

        if ($right) {
            foreach ($this->actions as $action) {
                $location = $action($context, $intent);

                if (isset($location)) {
                    return $director->handleLocation($location);
                }
            }

            if (isset($this->redirect)) {
                return $this->redirect->run($director, $context, $intent);
            }

        } elseif (isset($this->next)) {
            return $this->next->run($director, $context, $intent);
        }

        return $director->getConversation();
    }


    /*----------- actions -----------*/


    public function controller(string $controllerName, string $method) : self
    {
        $this->pushAction(function(Context $context, Intent $intent) use ($controllerName, $method) {
            $controller = $this->app->make($controllerName);
            return $controller->{$method}($context, $intent);
        });
        return $this;
    }

    public function call(callable $action) : self
    {
        $this->pushAction($action);
        return $this;
    }

    //todo
    public function callSelf(string $method) : self
    {
        $this->pushAction(function(Context $context, Intent $intent) use ($method) {
            $r = $context->callConfigMethod($method, $intent);
            return $r;
        });
        return $this;
    }

    public function reply(Message $message)  : self
    {
        $action = function(Context $context, Intent $intent) use ($message){
            $context->reply($message);
            return null;
        };
        $this->pushAction($action);
        return $this;
    }


    public function info(string $message, string $verbose = Message::NORMAL)  : self
    {
        $this->pushAction(function(Context $context, Intent $intent) use ($message, $verbose){
            $context->info($message, $verbose);
        });
        return $this;
    }


    public function warn(string $message, string $verbose = Message::NORMAL)  : self
    {
        $this->pushAction(function(Context $context, Intent $intent) use ($message, $verbose){
            $context->warn($message, $verbose);
            return null;
        });
        return $this;
    }

    public function error(string $message, string $verbose = Message::NORMAL) : self
    {
        $this->pushAction(function(Context $context, Intent $intent) use ($message, $verbose){
            $context->error($message, $verbose);
            return null;
        });
        return $this;
    }

    protected function pushAction(\Closure $action)
    {
        $this->actions[] = $action;
    }

}