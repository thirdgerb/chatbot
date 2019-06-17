<?php

/**
 * Class Dispatcher
 * @package Commune\Chatbot\Framework\Eventing
 */

namespace Commune\Chatbot\Framework\Predefined;


use Commune\Chatbot\Contracts\EventDispatcher;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Container\ContainerContract;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyDispatcher;

/**
 * 基于 Symfony dispatcher 实现事件机制
 */
class SymfonyEventDispatcher implements EventDispatcher
{

    /**
     * @var ContainerContract
     */
    protected $container;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * SymfonyEventDispatcher constructor.
     * @param ContainerContract $container
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerContract $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->dispatcher = new SymfonyDispatcher();
        $this->logger = $logger;
    }

    public function dispatchByName(string $eventName, Event $event = null): void
    {
        $this->dispatcher->dispatch($eventName, $event);
    }

    public function dispatch(Event $event) : void
    {
        //todo event
        $this->dispatcher->dispatch(get_class($event), $event);
    }

    public function listen(string $eventName, $listener) : void
    {
        if (is_string($listener)) {
            $this->listenClass($eventName, $listener);

        } elseif( is_callable($listener)) {
            $this->listenCallable($eventName, $listener);

        } else {
            throw new ConfigureException(
                'register listener for event '
                . $eventName
                . ' only allow class or callable, '
                . var_export($listener, true)
                . ' given'
            );
        }
    }

    public function listenCallable(
        string $eventName,
        callable $caller,
        int $priority = 0
    ): void
    {
        $this->dispatcher
            ->addListener(
                $eventName,
                $caller,
                $priority
            );
    }

    public function listenClass(
        string $eventName,
        string $clazzOrMethod
    ): void
    {

        $first = explode('@', $clazzOrMethod, 2);

        $clazz = $first[0];
        $methodDefined = $first[1] ?? 'handle#0';

        $second = explode('#', $methodDefined, 2);
        $method = $second[0] ?? 'handle';

        $priority = intval($second[1] ?? 0);

        $this->dispatcher
            ->addListener(
                $eventName,
                function(
                    Event $event,
                    string $eventName,
                    EventDispatcherInterface $dispatcher
                ) use ($clazz, $method) : void
                {
                    $handler = $this->container->make($clazz);
                    // 运行
                    call_user_func([$handler, $method], $event, $eventName, $dispatcher);
                },
                $priority
            );
    }


    public function __destruct()
    {
        if (CHATBOT_DEBUG) $this->logger->debug(__METHOD__);
    }
}