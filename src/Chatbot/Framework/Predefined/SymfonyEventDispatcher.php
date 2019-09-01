<?php

/**
 * Class Dispatcher
 * @package Commune\Chatbot\Framework\Eventing
 */

namespace Commune\Chatbot\Framework\Predefined;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Contracts\EventDispatcher;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * 基于 Symfony dispatcher 实现事件机制
 */
class SymfonyEventDispatcher implements EventDispatcher, RunningSpy, HasIdGenerator
{
    use RunningSpyTrait, IdGeneratorHelper;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * @var SymfonyDispatcher
     */
    protected $dispatcher;


    public function __construct()
    {
        $this->id = $this->createUuId();
        $this->dispatcher = new SymfonyDispatcher();

        static::addRunningTrace($this->id, $this->id);
    }

    public function withConversation(Conversation $conversation): EventDispatcherInterface
    {
        $this->conversation = $conversation;
        return $this;
    }


    public function dispatch(object $event)
    {
        $eventName = get_class($event);
        $eventObject = new GenericEvent($event);
        $this->dispatcher->dispatch($eventName, $eventObject);
    }


    public function listen(string $eventName, $listener) : void
    {
        if (is_string($listener) && class_exists($listener)) {
            $this->listenClassMethod($eventName, [$listener, '__invoke']);

        } elseif ( is_array($listener)) {
            $this->listenClassMethod($eventName, $listener);

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
                function(
                    GenericEvent $event,
                    string $eventName,
                    SymfonyDispatcherInterface $dispatcher
                ) use ($caller){

                    $this->call($caller, $event, $eventName, $dispatcher);

                },
                $priority
            );
    }

    public function listenClassMethod(
        string $eventName,
        array $classAndMethod,
        int $priority = 0
    ): void
    {

        $clazz = $classAndMethod[0] ?? '';
        $method = $classAndMethod[1] ?? '';

        if (empty($clazz) || empty($method) || !class_exists($clazz)) {
            throw new ConfigureException("register listener to event $eventName with bad definition that class is '$clazz' and method is '$method'");
        }

        $this->dispatcher
            ->addListener(
                $eventName,
                function(
                    GenericEvent $event,
                    string $eventName,
                    SymfonyDispatcherInterface $dispatcher
                ) use ($clazz, $method) : void
                {
                    $handler = $this->conversation->make($clazz);
                    // 运行
                    $this->call(
                        [$handler, $method],
                        $event,
                        $eventName,
                        $dispatcher
                    );
                },
                $priority
            );
    }

    /**
     * @param callable $caller
     * @param GenericEvent $event
     * @param string $eventName
     * @param SymfonyDispatcherInterface $dispatcher
     * @throws
     */
    protected function call(
        callable $caller,
        GenericEvent $event,
        string $eventName,
        SymfonyDispatcherInterface $dispatcher
    ) : void
    {
        $subject = $event->getSubject();
        $a = microtime(true);

        call_user_func($caller, $subject, $event);
        $b = microtime(true);
        var_dump($eventName, round(($b - $a) * 1000000));

    }

    public function __destruct()
    {
        static::removeRunningTrace($this->id);
    }
}