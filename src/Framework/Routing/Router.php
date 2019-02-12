<?php

/**
 * Class Router
 * @package Commune\Chatbot\Host\Routing
 */

namespace Commune\Chatbot\Framework\Routing;

use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Intent\IntentData;
use Commune\Chatbot\Framework\Routing\Predefined\MissMatchIR;
use Commune\Chatbot\Framework\Intent\IntentFactory;

class Router
{

    /*---- loaded ----*/

    protected $dialogRoutes = [];

    protected $intentMatchers = [];

    protected $contextConfigs = [];

    protected $contextScopeTypes = [];

    protected $contextDefinedData = [];

    protected $contextDefinedProps = [];

    /*---- cache ----*/

    protected $missMatchRoute;


    /*---- components ----*/

    /**
     * @var ChatbotApp
     */
    protected $app;

    /**
     * Router constructor.
     * @param ChatbotApp $app
     */
    public function __construct(ChatbotApp $app)
    {
        $this->app = $app;
    }

    /*------- intent -------*/

    public function addIntentMatcher(string $intentName)
    {
        $matcher = IntentFactory::makeByIntent($intentName);

        if (isset($matcher)) {
            $this->intentMatchers[$intentName] = $matcher;
        }
    }

    public function getIntentFactory(string $intentName) : ? IntentFactory
    {
        if (!isset($this->intentMatchers[$intentName])) {
            $this->addIntentMatcher($intentName);
        }
        return $this->intentMatchers[$intentName] ?? null;
    }

    public function getMissMatchIntentRoute() : MissMatchIR
    {
        if (!isset($this->missMatchRoute)) {
            $this->missMatchRoute = new MissMatchIR($this->app, $this);
        }
        return $this->missMatchRoute;
    }

    public function defaultRouteOfIntent(string $intentId) : IntentRoute
    {
        $factory = $this->getIntentFactory($intentId);

        if (isset($factory)) {
            return $factory->defaultRoute($this->app, $this);
        }

        return $this->getMissMatchIntentRoute();
    }

    /*------- dialogue -------*/

    private function addDialogRoute(string $contextName, DialogRoute $route)
    {
        $this->dialogRoutes[$contextName] = $route;
    }

    public function getDialogRoute(string $contextName) : DialogRoute
    {
        return $this->dialogRoutes[$contextName] ?? $this->loadDialogRouteByName($contextName);
    }


    /*------- context -------*/

    public function loadDialogRouteByName(string $contextName) : DialogRoute
    {
        if (isset($this->dialogRoutes[$contextName])) {
            return $this->dialogRoutes[$contextName];
        }

        $config = $this->loadContextConfig($contextName);
        $dialogRoute = new DialogRoute($this->app, $this, $contextName);
        call_user_func([$config, 'routing'], $dialogRoute);
        $this->addDialogRoute($contextName, $dialogRoute);
        return $dialogRoute;
    }

    public function loadContextConfig(string $contextName, ContextCfg $config = null) : ContextCfg
    {
        if (isset($this->contextConfigs[$contextName])) {
            return $this->contextConfigs[$contextName];
        }

        if (!isset($config)) {

            if (!class_exists($contextName)) {
                //todo
                throw new ConfigureException();
            }

            try {
                $r = new \ReflectionClass($contextName);
            } catch (\ReflectionException $e) {
                throw new ConfigureException('', 0, $e);
            }

            if (!$r->isSubclassOf(ContextCfg::class)) {
                //todo
                throw new ConfigureException();
            }
            // 允许依赖注入
            $config = $this->app->make($contextName);
        }

        return $this->contextConfigs[$contextName] = $config;
    }

    public function getContextId(string $contextName, Scope $scope) : string
    {
        $scopeTypes = $this->loadContextConfig($contextName)->getScopeTypes();

        $idStr = $contextName;

        foreach($scopeTypes as $type) {
            $idStr .= ":$type:".$scope->getScope($type);
        }

        return md5($idStr);
    }
}
