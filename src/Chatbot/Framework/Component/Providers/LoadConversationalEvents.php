<?php

/**
 * Class LoadConversationalEvents
 * @package Commune\Chatbot\Framework\Component\Providers
 */

namespace Commune\Chatbot\Framework\Component\Providers;


use Commune\Chatbot\Contracts\EventDispatcher;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Container\ContainerContract;

class LoadConversationalEvents extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    /**
     * @var array
     */
    protected $events;

    public function __construct(ContainerContract $app, array $events)
    {
        $this->events = $events;
        parent::__construct($app);
    }

    /**
     * @param ContainerContract $app
     */
    public function boot($app)
    {
        /**
         * @var EventDispatcher $dispatcher
         */
        $dispatcher = $app->get(EventDispatcher::class);
        foreach ($this->events as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                $dispatcher->listen($eventName, $listener);
            }
        }
    }

    public function register()
    {
    }


}