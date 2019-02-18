<?php

/**
 * Class DialogueRoute
 * @package Commune\Chatbot\Host\Routing
 */

namespace Commune\Chatbot\Framework\Routing;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Routing\Predefined\FallbackIR;
use Commune\Chatbot\Framework\Routing\Predefined\PreparedIR;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Intent\IntentFactory;

class DialogRoute
{

    /*------ components ------*/

    /**
     * @var ChatbotApp
     */
    protected $app;

    /**
     * @var Router
     */
    protected $router;

    /*------ properties ------*/


    /**
     * @var IntentRoute
     */
    protected $startRoute;

    /**
     * @var IntentRoute
     */
    protected $fallbackRoute;

    /**
     * @var IntentRoute
     */
    protected $preparedRoute;

    protected $intentRoutes = [];

    /**
     * @var string
     */
    protected $contextName;



    /*------ construct ------*/


    /**
     * DialogRoute constructor.
     * @param ChatbotApp $app
     * @param Router $router
     * @param string $contextName
     */
    public function __construct(ChatbotApp $app, Router $router, string $contextName)
    {
        $this->app = $app;
        $this->router = $router;
        $this->contextName = $contextName;
    }

    public function getContextName() : string
    {
        return $this->contextName;
    }

    public function setFallbackRoute(IntentRoute $route)
    {
        $this->fallbackRoute = $route;
    }

    /**
     * @return IntentRoute
     */
    public function getFallbackRoute() : IntentRoute
    {
        return $this->fallbackRoute;
    }


    /*------ construct ------*/

    public function availableRoutes(Context $context) : array
    {
        $result = [];
        foreach ($this->intentRoutes as $route) {
            /**
             * @var IntentRoute $route
             */
            if ($route->isAvailable($context)) {
                $result[] = $route;
            }
        }
        return $result;
    }



    /*--------- 创建路由 ---------*/

    public function prepared() : PreparedIR
    {
        if (!isset($this->preparedRoute)) {
            $this->preparedRoute = new PreparedIR(
                $this->app,
                $this->router
            );
        }
        return $this->preparedRoute;
    }

    public function fallback() :  FallbackIR
    {
        $this->fallbackRoute = new FallbackIR(
            $this->app,
            $this->router
        );

        return $this->fallbackRoute;
    }

    public function name(string $intentId) : IntentRoute
    {
        // 无法匹配和产生数据的.
        $intentRoute = new IntentRoute($this->app, $this->router, $intentId);
        return $this->addIntentRoute($intentRoute);
    }


    /**
     * @param string $intentName
     * @return IntentRoute
     * @throws \ReflectionException
     */
    public function hearsIntent(string $intentName) : IntentRoute
    {
        $intentCfg  = $this->router->loadIntentConfig($intentName);
        $intentRoute = new IntentRoute($this->app, $this->router, $intentName);
        $intentRoute->hearsIntent($intentCfg);
        return $this->addIntentRoute($intentRoute);
    }

    public function hearsCommand(string $signature, string $description = '') : IntentRoute
    {
        $factory = new IntentFactory();
        $factory->setCommand($signature);
        $intentRoute = new IntentRoute($this->app, $this->router, $factory->getCommandName(), $description, $factory);
        return $this->addIntentRoute($intentRoute);
    }

    /**
     * 精确匹配
     * @param string $exactly
     * @param string $description
     * @return IntentRoute
     */
    public function hears(string $exactly, string $description = '') : IntentRoute
    {
        $intentRoute = new IntentRoute($this->app, $this->router, $exactly, $description);
        $intentRoute->exactly($exactly);
        return $this->addIntentRoute($intentRoute);
    }


    public function getIntentRoute(string $intentId) : ? IntentRoute
    {
        return $this->intentRoutes[$intentId] ?? null;
    }


    protected function addIntentRoute(IntentRoute $route) : IntentRoute
    {
        $this->intentRoutes[$route->getId()] = $route;
        return $route;
    }


    /*--------- 外部方法 ---------*/

    public function match(Context $context, Conversation $conversation) : IntentRoute
    {
        if ($this->getContextName() !== $context->getName()) {
            //todo
            throw new \RuntimeException();
        }

        // 检查是否有预设好的意图.
        $possibleIntents = $conversation->getPossibleIntents();
        $hasPossibleIntent = !$possibleIntents->isEmpty();
        $possibleIntentNames = $possibleIntents->map(function($item){
            /**
             * @var Intent $item
             */
            return $item->getId();
        });

        foreach ($this->intentRoutes as $intentRoute) {
            /**
             * @var IntentRoute $intentRoute
             */

            if (! $intentRoute->isAvailable($context)) {
                break;
            }

            // 检查possible
            if ($hasPossibleIntent) {
                $listenTo = $intentRoute->getListenIntent();
                if (isset($listenTo) && $possibleIntentNames->contains($listenTo)) {
                    $intent = $possibleIntents[$listenTo];
                    return $this->matched($intentRoute, $intent, $conversation);
                }

            //没有预设好的意图时, 才单个检查.
            } else {
                // 单个检查
                $intent = $intentRoute->match($conversation);
                if (isset($intent)) {
                    return $this->matched($intentRoute, $intent, $conversation);
                }
            }
        }


        // 没有任何命中的时候才跳出.
        return $this->dialogMissMatched($conversation);
    }


    protected function dialogMissMatched(Conversation $conversation) : IntentRoute
    {
        // 语境的fallback 优先于预设意图.
        if (isset($this->fallbackRoute)) {
            $conversation->setMatchedIntent($conversation->defaultIntent());
            return $this->fallbackRoute;
        }

        // 有预设好意图时, 执行该意图预设好的方法.
        $possibleIntents = $conversation->getPossibleIntents();
        if (!$possibleIntents->isEmpty()) {
            /**
             * 获取优先级最高的意图
             * @var Intent $firstIntent
             */
            $firstIntent = reset($possibleIntents);
            $defaultRoute = $this->router->defaultRouteOfIntent($firstIntent);
            return $this->matched($defaultRoute, $firstIntent, $conversation);
        }

        $conversation->setMatchedIntent($conversation->defaultIntent());
        return $this->router->getMissMatchIntentRoute();
    }

    protected function matched(IntentRoute $route, Intent $intent, Conversation $conversation) : IntentRoute
    {
        $conversation->setMatchedIntent($intent);
        return $route;
    }


}