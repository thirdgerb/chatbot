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
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Intent\IntentCfg;
use Commune\Chatbot\Framework\Routing\Predefined\MissMatchIR;
use Commune\Chatbot\Framework\Intent\IntentFactory;

class Router
{

    /*---- loaded ----*/

    protected $dialogRoutes = [];

    protected $intentConfigs = [];

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


    /*------- intentCfg -------*/

    /**
     * @param string $intentCfgName
     * @return mixed
     * @throws \ReflectionException
     */
    public function loadIntentConfig(string $intentCfgName) : IntentCfg
    {
        if (isset($this->intentConfigs[$intentCfgName])) {
            return $this->intentConfigs[$intentCfgName];
        }

        if (!class_exists($intentCfgName)) {
            //todo
            throw new ConfigureException();
        }

        $r = new \ReflectionClass($intentCfgName);
        if (!$r->isSubclassOf(IntentCfg::class)) {
            throw new ConfigureException();
        }

        $this->intentConfigs[$intentCfgName] = $config = $this->app->make($intentCfgName);
        return $config;
    }

    public function hasIntentConfig(string $intentId)
    {
        return isset($this->intentConfigs[$intentId]);
    }


    /*------- intentRoute -------*/

    public function getMissMatchIntentRoute() : MissMatchIR
    {
        if (!isset($this->missMatchRoute)) {
            $this->missMatchRoute = new MissMatchIR($this->app, $this);
        }
        return $this->missMatchRoute;
    }

    /**
     * @param Intent $intent
     * @return IntentRoute
     * @throws \ReflectionException
     */
    public function defaultRouteOfIntent(Intent $intent) : IntentRoute
    {
        $defaultRoute = null;
        if ($this->hasIntentConfig($intent->getId())) {
            /**
             * @var IntentCfg $intentCfg
             */
            $intentCfg = $this->loadIntentConfig($intent->getId());
            $defaultRoute = $intentCfg->defaultRoute($this->app, $this);
        }

        return $defaultRoute ??  $this->getMissMatchIntentRoute();
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
