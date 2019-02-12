<?php

/**
 * Class Session
 * @package Commune\Chatbot\Host\Session
 */

namespace Commune\Chatbot\Framework\Session;


use Commune\Chatbot\Contracts\ChatbotApp;
use Commune\Chatbot\Contracts\SessionDriver;
use Commune\Chatbot\Framework\Context\Context;
use Commune\Chatbot\Framework\Context\ContextData;
use Commune\Chatbot\Framework\Conversation\Conversation;
use Commune\Chatbot\Framework\Conversation\Scope;
use Commune\Chatbot\Framework\Directing\History;
use Commune\Chatbot\Framework\Directing\Location;
use Commune\Chatbot\Framework\Routing\Router;
use Psr\Log\LoggerInterface;

class Session
{

    /*------ components ------*/

    /**
     * @var ChatbotApp
     */
    protected $app;

    /**
     * @var SessionDriver
     */
    protected $driver;

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Scope
     */
    protected $scope;

    /**
     * @var LoggerInterface
     */
    protected $log;


    /*------ cache ------*/

    protected $contextsData = [];

    /**
     * @var History
     */
    protected $history;


    /*------ construct -------*/

    public function __construct(
        ChatbotApp $app,
        SessionDriver $driver,
        LoggerInterface $log,
        Router $router,
        Conversation $conversation
    )
    {
        $this->app = $app;
        $this->router = $router;
        $this->driver = $driver;
        $this->conversation = $conversation;
        $this->log = $log;
        $this->scope = $conversation->getScope();
    }

    /*------ history -------*/


    public function getHistory() : History
    {

        if (!isset($this->history)) {
            $id = $this->conversation->getSessionId();
            $history = $this->driver->loadHistory($id);
            $root = $this->app->getRootContext();
            $this->history = $history ? : new History(
                $this->scope->getChatId(),
                $this->scope->getSessionId(),
                new Location($root, [], $this->makeContextId($root))
            );
        }

        return $this->history;
    }

    /*------ dialog -------*/


    public function makeContextId(string $contextName) : string
    {
        return $this->router->getContextId($contextName, $this->scope);
    }

    /**
     * when return null, context never saved
     *
     * @param string $id
     * @return Context|null
     */
    public function fetchContextById(string $id = null) : ? Context
    {
        if (!isset($id)) {
            return null;
        }
        $data = $this->fetchContextDataById($id);
        return isset($data) ? $this->makeContext($data) : null;
    }

    public function fetchContextByLocation(Location $location) : Context
    {
        $context = $this->fetchContextById($location->getContextId());

        if (isset($context)) {
            return $context;
        }

        $context = $this->createContext(
            $location->getContextName(),
            $location->getProps()
        );
        // 创建时的事件.
        return $context;

    }


    public function createContext(
        string $contextName,
        array $props
    ) : Context
    {
        $id = $this->makeContextId($contextName);

        $contextConfig = $this->router->loadContextConfig($contextName);
        $contextData = new ContextData(
            $id,
            $contextName,
            $contextConfig->getDataSchema(),
            $contextConfig->getPropsSchema(),
            $this->scope,
            $props
        );

        $this->contextsData[$id] = $contextData;
        return $this->makeContext($contextData);
    }

    public function fetchOrCreateContext(
        string $contextName,
        array $props,
        string $id = null
    ) : Context
    {
        $context = null;
        if (isset($id)) {
            $context = $this->fetchContextById($id);
        }

        if (isset($context)) {
            return $context;
        }

        return $this->createContext($contextName, $props);
    }

    private function makeContext(ContextData $data) : ? Context
    {
        $name = $data->getContextName();
        $config = $this->router->loadContextConfig($name);
        return new Context($data, $this, $config);
    }

    public function fetchContextDataById(string $id) : ? ContextData
    {
        if (array_key_exists($id, $this->contextsData)) {
            return $this->contextsData[$id];
        }

        return $this->contextsData[$id] = $this->driver->fetchContextDataById($id);
    }


    /*------ conversation -------*/

    public function getConversation() : Conversation
    {
        return $this->conversation;
    }


    /*------ save -------*/

    /**
     * 1. save information
     * 2. save dialogObj
     */
    public function save()
    {
        $id = $this->conversation->getSessionId();
        //history
        if (isset($this->history)) {
            $this->driver->saveHistory($id, $this->history);
        }
        //data
        foreach ($this->contextsData as $id => $data) {
            /**
             * @var ContextData $data
             */
            if (!empty($data) && $data->needSave()) {
                $this->driver->saveContextData($data);
            }
        }
    }

}