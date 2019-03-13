<?php

/**
 * Class IntentRoute
 * @package Commune\Chatbot\Host\Routing
 */

namespace Commune\Chatbot\Framework\Routing;


use Commune\Chatbot\Framework\Context\Predefined\FulfillIntent;
use Commune\Chatbot\Framework\Directing\Location;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Intent\IntentCfg;
use Commune\Chatbot\Framework\Intent\Matchers\Matcher;
use Commune\Chatbot\Framework\Routing\Runners\Action;
use Commune\Chatbot\Framework\Routing\Runners\Redirect;
use Commune\Chatbot\Framework\Support\Pipeline;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Directing\Director;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Intent\IntentFactory;
use Symfony\Component\Console\Input\InputArgument;

class IntentRoute
{
    /**
     * @var ChatbotApp
     */
    protected $app;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var IntentFactory
     */
    protected $intentFactory;

    /**
     * @var Pipeline | null
     */
    protected $pipeline;

    /**
     * @var string
     */
    protected $description;

    /*----- 运算属性 -----*/


    /**
     * @var Action
     */
    protected $action;

    /**
     * @var callable
     */
    protected $availableCondition;

    protected $middleware = [];

    /**
     * IntentRoute constructor.
     * @param ChatbotApp $app
     * @param Router $router
     * @param string $id
     * @param string $description
     * @param IntentFactory|null $factory
     */
    public function __construct(
        ChatbotApp $app,
        Router $router,
        string $id,
        string $description = '',
        IntentFactory $factory = null
    )
    {
        $this->id = $id;
        $this->app = $app;
        $this->router = $router;
        $this->description = $description;
        $this->intentFactory = $factory ?? new IntentFactory();
    }

    /**
     * @return string
     */
    public function getListenIntent(): ? string
    {
        return $this->intentFactory->getIntentName();
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    /*------- 更多match -------*/

    public function hearsIntent(IntentCfg $intentCfg) : self
    {
        $this->description = $intentCfg->getDescription();
        $this->intentFactory->setIntentCfg($intentCfg);
        return $this;
    }

    public function hearsRegex(...$regex) : self
    {
        $this->intentFactory->addRegex($regex);
        return $this;
    }

    public function hearsMatcher(Matcher $matcher) : self
    {
        $this->intentFactory->addMatcher($matcher);
        return $this;
    }

    public function hearsCommand(string $signature) : self
    {
        $this->intentFactory->setCommand($signature);
        return $this;
    }

    public function exactly(string $alias) : self
    {
        $this->intentFactory->addExactly($alias);
        return $this;
    }


    /*------- 重定向 -------*/

    public function middleware($middleware) : self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

    /*------- 定义逻辑 -------*/


    /**
     * @param array|callable|null $condition
     * @return callable
     */
    public static function warpCondition($condition) : ? callable
    {
        // 可运行, 直接使用.
        if (is_null($condition)) {
            return null;
        } elseif (is_callable($condition)) {
            return $condition;

        // 参数等于的情况.
        } elseif (is_array($condition)) {
            return function(Context $context) use ($condition) {
                foreach($condition as $key => $val) {
                    if ($context->fetch($key) != $val) {
                        return false;
                    }
                }

                return true;
            };
        } else {
            //todo
            throw new ConfigureException();
        }
    }

    /**
     * @param string|array|callable $condition
     * @return self
     */
    public function availableWhen($condition) : self
    {
        $this->availableCondition = static::warpCondition($condition);
        return $this;
    }


    public function action($condition = null) : Action
    {
        if (!isset($this->action)) {
            $this->action = new Action($this->app, $condition);
        }
        return $this->action;
    }

    public function redirect($condition = null) : Redirect
    {
        return $this->action()->redirect($condition);
    }


    /*------- 运行逻辑 -------*/

    public function isAvailable(Context $context) :bool
    {
        if (!isset($this->availableCondition)) {
            return true;
        }
        return call_user_func($this->availableCondition, $context);
    }


    /**
     * @param string|array|callable $condition
     * @return callable
     */
    public static function wrapCondition($condition) : callable
    {
        // 可运行, 直接使用.
        if (is_null($condition)) {
            return static::class.'::beTrue';

        } elseif (is_callable($condition)) {
            return $condition;

            // 参数等于的情况.
        } elseif (is_array($condition)) {
            return function(Context $context) use ($condition) {
                foreach($condition as $key => $val) {
                    if ($context->fetch($key) != $val) {
                        return false;
                    }
                }

                return true;
            };
        } else {
            //todo
            throw new ConfigureException();
        }
    }

    final public static function beTrue() : bool
    {
        return true;
    }

    public function match(Conversation $conversation) : ? Intent
    {
        return isset($this->intentFactory) ? $this->intentFactory->match($conversation) : null;
    }

    public function run(
        Context $context,
        Conversation $conversation,
        Director $director
    ) : Conversation
    {
        if (empty($this->middleware)) {
            return $this->doRun($context, $director, $conversation);
        }

        $pipeline = new Pipeline($this->app, $this->middleware);
        //中间件优先
        $pipeline->setUpPipe(function(Conversation $passable) use ($context, $director) {
            $intent = $passable;
            return $this->doRun($context, $director, $passable);
        });
        return $pipeline->send($conversation);
    }

    public function doRun(
        Context $context,
        Director $director,
        Conversation $conversation
    ) {

        $intent = $conversation->getMatchedIntent();
        $location = $this->fulfillIntent($intent);
        if (isset($location)) {
            //保证回调到的是同一个路由.
            $intended = $context->getLocation();
            $location->setCallbackIntentId($this->getId());
            $location->pushIntended($intended);
            $conversation->setMatchedIntent($conversation->defaultIntent());

            return $director->handleLocation($location);
        }

        return $this->action()->run(
            $director,
            $context,
            $intent
        );
    }

    protected function fulfillIntent(Intent $intent) : ? Location
    {
        $arguments = $intent->dependingArguments();
        if (empty($arguments)) {
            return null;
        }

        $intentId = $intent->getId();
        $intentEntities = $intent->getEntities();
        $questions = [];

        $questionTemp = $this->app->getConfig(ChatbotApp::MESSAGES_ASK_INTENT_ARGUMENT, '请输入');

        foreach($arguments as $argument) {

            /**
             * @var InputArgument $argument
             */
            $questionTemp = str_replace('{key}', $argument->getName(), $questionTemp);
            $questionTemp = str_replace('{desc}', $argument->getDescription(), $questionTemp);
            $questions[$argument->getName()] = [
                'ask',
                $questionTemp,
                $argument->getDefault()
            ];
        }

        $location = new Location(FulfillIntent::class, [
            'intentId' => $intentId,
            'intentEntities' => $intentEntities,
            'questions' => $questions
        ]);
        return $location;
    }
}