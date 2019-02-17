<?php

/**
 * Class Director
 * @package Commune\Chatbot\Host\Direction
 */

namespace Commune\Chatbot\Framework\Directing;

use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Framework\Context\ContextData;
use Commune\Chatbot\Framework\Directing\SpecialLocations\Intending;
use Commune\Chatbot\Framework\Directing\SpecialLocations\Replace;
use Commune\Chatbot\Framework\Directing\SpecialLocations\Guesting;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextCfg;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Routing\Router;
use Commune\Chatbot\Framework\Session\Session;
use Commune\Chatbot\Framework\Intent\Intent;
use Commune\Chatbot\Framework\Support\ChatbotUtils;
use Psr\Log\LoggerInterface;

class Director
{
    /**
     * @var ChatbotApp
     */
    protected $app;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var History
     */
    protected $history;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * @var int
     */
    protected $tick;

    /**
     * @var int
     */
    protected $maxTicks;

    /**
     * @var Scope
     */
    protected $scope;

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * @var Location
     */
    protected $lastLocation;

    /**
     * Director constructor.
     * @param ChatbotApp $app
     * @param Session $session
     * @param Router $router
     */
    public function __construct(ChatbotApp $app, Session $session, Router $router)
    {
        $this->app = $app;
        $this->session = $session;
        $this->history = $this->session->getHistory();
        $this->conversation = $this->session->getConversation();
        $this->router = $router;
        $this->log = $app->make(LoggerInterface::class);
        $this->tick = 0;
        $this->maxTicks = $app->getConfig(ChatbotApp::RUNTIME_MAX_DIRECT, 10);
        $this->scope = $this->session->getConversation()->getScope();
    }

    /**
     * @return Conversation
     */
    public function getConversation(): Conversation
    {
        return $this->conversation;
    }

    public function makeLocation(string $contextName, array $props)  : Location
    {
        $id = $this->router->getContextId($contextName, $this->scope);
        return new Location($contextName, $props, $id);
    }

    public function dispatch() : Conversation
    {
        try {
            $this->ticks();
            $current = $this->history->current();
            return $this->startDialog($current, function (Context $context) {
                $dialogRoute = $this->router->getDialogRoute($context->getName());
                $intentRoute = $dialogRoute->match($context, $this->conversation);
                return $intentRoute->run($context, $this->conversation, $this);
            });
        } catch (RedirectionBreak $e) {

            $location = $e->getRedirection();
            $this->history->to($location);
            return $this->startDialog($location);

        } catch (TooManyDirectingException $e) {
            $this->history->flush();
            throw $e;
        }
    }

    public function cancel() : Conversation
    {
        $current = $this->history->current();
        $last = $current;
        while($intended = $last->getIntended()) {
            $last = $intended;
        }
        $context = $this->fetchContext($last);
        $this->fireContextEvent($context, ContextCfg::CANCELED);
        return $this->backward();
    }

    public function failed() : Conversation
    {
        $this->ticks();
        $current = $this->history->current();
        $last = $current;
        while($intended = $last->getIntended()) {
            $last = $intended;
        }
        $context = $this->fetchContext($last);
        $this->fireContextEvent($context, ContextCfg::FAILED);
        return $this->backward();
    }

    public function home() : Conversation
    {
        $this->ticks();
        $location = $this->history->home();
        return $this->startDialog($location);
    }


    public function to(Location $location) : Conversation
    {
        $this->ticks();
        $this->history->to($location);
        return $this->startDialog($location);
    }

    public function forward() : Conversation
    {
        $this->ticks();
        $location = $this->history->forward();
        return $this->startDialog($location);
    }

    public function backward() : Conversation
    {
        $this->ticks();
        $location = $this->history->backward();
        return $this->startDialog($location);
    }

    public function guest(Location $to, Location $from = null, string $callback = null) : Conversation
    {
        $this->ticks();

        if (!$from) {
            $from = $this->history->current();
        }

        if ($callback) {
            $to->setCallbackIntentId($callback);
        }

        $to->pushIntended($from);
        $this->history->setCurrent($to);
        return $this->startDialog($to);
    }

    public function repeat() : Conversation
    {
        $current = $this->history->getCurrent();
        return $this->startDialog($current);
    }

    public function replace(Location $replace) : Conversation
    {
        $this->ticks();
        $this->history->setCurrent($replace);
        return $this->startDialog($replace);
    }

    public function intended(Intent $callbackIntent = null) : Conversation
    {
        // count redirection
        $this->ticks();

        // if current not has callback location, backward
        $current = $this->history->current();
        $intended = $current->getIntended();
        if (!isset($intended)) {
            return $this->backward();
        }

        // replace current by intended
        $this->history->setCurrent($intended);

        // has no callback intent, then just start dialog
        $callback = $current->getCallbackIntentId();
        if (!isset($callback)) {
            return $this->startDialog($intended);
        }

        // if has callback intent, fetch the intentRoute and run
        return $this->startDialog($intended, function(Context $intendedContext) use ($callback, $current) {
            $intendedDialogRoute = $this->router->getDialogRoute($intendedContext->getName());
            $callbackRoute = $intendedDialogRoute->getIntentRoute($callback);

            if (empty($callbackRoute)) {
                throw new ConfigureException();
            }

            // if not defined callback intent, make context data as callback intent
            if (!isset($callbackIntent)) {
                //todo
                $callbackIntent = $this->fetchContext($current)->toIntent(); // 将回调对象当成入参
            }
            $this->conversation->setMatchedIntent($callbackIntent);
            return $callbackRoute->run($intendedContext, $this->conversation, $this);
        });
    }

    public function handleLocation(Location $location) : Conversation
    {
        if ($location instanceof Guesting) {
            return $this->guest($location);
        }

        if ($location instanceof Intending) {
            $intent = $location->getPredefinedIntent();
            return $this->intended($intent);
        }

        if ($location instanceof Replace) {
            return $this->replace($location);
        }

        return $this->to($location);
    }

    /*--------- status ----------*/

    protected function startDialog(Location $location, \Closure $callback = null) : Conversation
    {
        $context = $this->fetchContext($location);

        switch($context->getDataStatus()) {
            case ContextData::CREATED :
                $this->fireContextEvent($context, ContextCfg::CREATING);
                break;
            case ContextData::WAKED :
                $this->fireContextEvent($context, ContextCfg::WAKING);
                break;
            case ContextData::DEAD :
                $this->fireContextEvent($context, ContextCfg::RESTORING);
                break;
        }

        if ($dependency = $context->initDepending()) {
            $this->fireContextEvent($context, ContextCfg::DEPENDING);
            return $this->guest($dependency, $location);
        }

        // 在这个环节回调.
        if (isset($callback)) {
            return $callback($context);
        }

        $dialogRoute = $this->router->getDialogRoute($context->getName());
        $preparedRoute = $dialogRoute->prepared();
        return $preparedRoute->run($context, $this->conversation, $this);
    }

    public function fetchCurrentContext() : Context
    {
        return $this->fetchContext($this->history->current());
    }

    public function fetchContext(Location $location) : Context
    {
        $context = $this->session->fetchContextByLocation($location);
        $locationId = $location->getContextId();
        if (!isset($locationId)) {
            $location->setContextId($context->getId());
        }
        return $context;
    }

    protected function ticks()
    {
        $this->debug();
        $this->tick++;
        if ($this->tick > $this->maxTicks) {
            //todo
            $this->log->error($message = 'too match redirection : '.$this->tick);
            throw new TooManyDirectingException($message);
        }
    }

    protected function debug()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $func = $backtrace[2]['function'];
        $this->log->debug('Host Director run ' . $func, $backtrace[2]);
    }

    protected function fireContextEvent(Context $context, string $event)
    {
        if (!in_array($event, ContextCfg::EVENTS)) {
            //todo
            throw new ConfigureException();
        }


        $data = $this->session->fetchContextDataById($context->getId());
        $data->handleContextEvent($event);

        $config = $this->router->loadContextConfig($data->getContextName());
        call_user_func([$config, $event], $context);
    }
}