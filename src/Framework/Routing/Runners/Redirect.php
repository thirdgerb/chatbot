<?php

/**
 * Class Redirect
 * @package Commune\Chatbot\Framework\Routing\Runners
 */

namespace Commune\Chatbot\Framework\Routing\Runners;


use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Directing\Director;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Routing\IntentRoute;

class Redirect extends Runner
{
    /**
     * @var callable
     */
    protected $condition;

    /**
     * @var Action
     */
    protected $action;

    /**
     * @var callable
     */
    protected $redirect;

    /**
     * @var Redirect
     */
    protected $next;

    /**
     * Redirect constructor.
     * @param callable|array $condition
     * @param Action $action
     */
    public function __construct( Action $action, $condition = null)
    {
        $this->action = $action;
        $this->condition = IntentRoute::wrapCondition($condition);
    }

    /*----------- to -----------*/

    public function action($condition = null) : Action
    {
        return $this->action->action($condition);
    }

    public function redirect($condition = null) : Redirect
    {
        return $this->next = new Redirect($this->action, $condition);
    }

    /*----------- run -----------*/

    public function run(Director $director, Context $context, Intent $intent): Conversation
    {
        $right = !isset($this->condition) || call_user_func($this->condition, $context);

        if ($right) {

            if (isset($this->redirect)) {
                return call_user_func($this->redirect, $director, $context, $intent);
            }

        } elseif (isset($this->next)) {
            return $this->next->run($director, $context, $intent);

        }

        return $director->getConversation();
    }

    /*----------- redirect -----------*/

    public function restart()
    {
        $this->setRedirect(function(Director $director, Context $context, Intent $intent) use ($contextName, $props){
            return $director->restart();
        });
        return $this;
    }

    public function to(string $contextName, array $props = []) : self
    {
        $this->setRedirect(function(Director $director, Context $context, Intent $intent) use ($contextName, $props){
            $location = $director->makeLocation($contextName, $props);
            return $director->to($location);
        });
        return $this;
    }

    public function intended(callable $warpIntent = null) : self
    {
        $this->setRedirect(function(Director $director, Context $context, Intent $intent) use ($warpIntent) {

            $newIntent = null;
            if (isset($warpIntent)) {
                $newIntent = $warpIntent($context, $intent);
                if (!$newIntent instanceof Intent) {
                    $newIntent = null;
                }
            } else {
                $newIntent = $context->toIntent();
            }

            return $director->intended($newIntent);
        });
        return $this;
    }

    public function guest(string $contextName, array $props = [], string $callback = null) : self
    {
        $this->setRedirect(function(Director $director, Context $context, Intent $intent) use ($contextName, $props, $callback){
            $location = $director->makeLocation($contextName, $props);
            return $director->guest($location, null, $callback);
        });
        return $this;
    }

    public function home() : self
    {
        $this->setRedirect(function(Director $director, Context $context, Intent $intent){
            return $director->home();
        });
        return $this;
    }

    public function forward() : self
    {
        $this->setRedirect(function(Director $director, Context $context, Intent $intent){
            return $director->forward();
        });
        return $this;
    }

    public function backward() : self
    {
        $this->setRedirect(function(Director $director){
            return $director->backward();
        });
        return $this;
    }

    public function then(callable $factory) : self
    {
        $this->setRedirect(function(Director $director, Context $context, Intent $intent) use ($factory) {
            $location = $factory($context, $intent);
            return $director->handleLocation($location);
        });
        return $this;
    }

    public function ask(string $callbackRoute, string $question, string $default = null, array $fields)
    {
        $this->setRedirect(function (
            Director $director,
            Context $context,
            Intent $intent
        ) use ($callbackRoute, $question, $default, $fields){
            if (!empty($fields)) {
                $question = $context->format($question, $fields);
            }
            $location = $context->ask($callbackRoute, $question, $default);
            return $director->handleLocation($location);
        });
        return $this;
    }

    public function confirm(string $callbackRoute, string $question, string $default = null, array $fields)
    {
        $this->setRedirect(function (
            Director $director,
            Context $context,
            Intent $intent
        ) use ($callbackRoute, $question, $default, $fields){
            if (!empty($fields)) {
                $question = $context->format($question, $fields);
            }
            $location = $context->confirm($callbackRoute, $question, $default);
            return $director->handleLocation($location);
        });
        return $this;
    }


    public function choose(string $callbackRoute, string $question, array $choices, int $default = 0, array $fields)
    {
        $this->setRedirect(function (
            Director $director,
            Context $context,
            Intent $intent
        ) use ($callbackRoute, $question, $choices, $default, $fields){
            if (!empty($fields)) {
                $question = $context->format($question, $fields);
            }
            $location = $context->choose($callbackRoute, $question, $choices, $default);
            return $director->handleLocation($location);
        });
        return $this;
    }

    public function setRedirect(\Closure $redirect)
    {
        $this->redirect = $redirect;
    }


}