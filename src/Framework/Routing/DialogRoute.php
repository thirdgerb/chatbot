<?php

/**
 * Class DialogueRoute
 * @package Commune\Chatbot\Host\Routing
 */

namespace Commune\Chatbot\Framework\Routing;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Routing\Predefined\FallbackIR;
use Commune\Chatbot\Framework\Routing\Predefined\PreparedIR;
use Commune\Chatbot\Framework\Intent\IntentData;
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
            if ($route->available($context)) {
                $result[] = $route;
            }
        }
        return $result;
    }



    /*--------- 创建路由 ---------*/

    public function prepared() : IntentRoute
    {
        if (!isset($this->preparedRoute)) {
            $this->preparedRoute = new PreparedIR(
                $this->app,
                $this->router,
                static::class
            );
        }
        return $this->preparedRoute;
    }

    public function fallback() :  IntentRoute
    {
        $this->fallbackRoute = new FallbackIR(
            $this->app,
            $this->router,
            static::class
        );

        return $this->fallbackRoute;
    }

    public function hearsIntent(string $intentName) : IntentRoute
    {
        $matcher = $this->router->getIntentFactory($intentName);

        if (!isset($matcher)) {
            //todo
            throw new ConfigureException();
        }

        $intent = new IntentRoute($this->app, $this->router, $intentName, $matcher);
        $this->addIntentRoute($intent);
        return $intent;
    }

    public function hearsCommand(string $signature) : IntentRoute
    {
        $id = md5($signature);

        $matcher = new IntentFactory();
        $matcher->addCommand($signature);
        $intent = new IntentRoute($this->app, $this->router, $id, $matcher);
        $this->addIntentRoute($intent);
        return $intent;
    }

    public function hearsRegex(...$regex) : IntentRoute
    {
        $id = md5(json_encode($regex));

        $matcher = new IntentFactory();
        $matcher->addRegex($regex);
        $intent = new IntentRoute($this->app, $this->router, $id, $matcher);
        $this->addIntentRoute($intent);
        return $intent;
    }

    private function addIntentRoute(IntentRoute $route)
    {
        $this->intentRoutes[$route->getId()] = $route;
    }

    public function getIntentRoute(string $intentId) : ? IntentRoute
    {
        return $this->intentRoutes[$intentId] ?? null;
    }


    /*--------- 外部方法 ---------*/

    public function match(Context $context, Conversation $conversation) : IntentRoute
    {
        if ($this->getContextName() !== $context->getName()) {
            //todo
            throw new \RuntimeException();
        }

        // available
        $availableRoutes = $this->availableRoutes($context);

        if (empty($availableRoutes)) {
            return $this->dialogMissMatched($conversation);
        }

        $possibleIntents = $conversation->getPossibleIntents();

        // possible
        if (!empty($possibleIntents)) {
            $possibleIntentNames = array_keys($possibleIntents);

            foreach($availableRoutes as $route) {
                /**
                 * @var IntentRoute $route
                 */
                $listenTo = $route->getListenIntent();
                if (in_array($listenTo, $possibleIntentNames)) {
                    $intent = $possibleIntents[$listenTo];
                    return $this->matched($route, $intent, $conversation);
                }
            }

            /**
             * @var IntentData $firstIntent
             */
            $firstIntent = reset($possibleIntents);
            $defaultRoute = $this->router->defaultRouteOfIntent($firstIntent->getId());
            return $this->matched($defaultRoute, $firstIntent, $conversation);
        }

        // matcher
        foreach ($availableRoutes as $route) {
            /**
             * @var IntentRoute $route
             */
            $intentFactory = $route->getIntentFactory();
            $intent = isset($intentFactory) ? $intentFactory->match($conversation->getMessage()) : null;
            if (isset($intent)) {
                return $this->matched($route, $intent, $conversation);
            }
        }

        return $this->dialogMissMatched($conversation);
    }


    protected function dialogMissMatched(Conversation $conversation) : IntentRoute
    {
        $conversation->setMatchedIntent($conversation->defaultIntent());
        if (isset($this->fallbackRoute)) {
            return $this->fallbackRoute;
        }

        return $this->router->getMissMatchIntentRoute();
    }

    protected function matched(IntentRoute $route, IntentData $intent, Conversation $conversation) : IntentRoute
    {
        $conversation->setMatchedIntent($intent);
        return $route;
    }


}