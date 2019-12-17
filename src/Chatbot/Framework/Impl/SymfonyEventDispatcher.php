<?php

namespace Commune\Chatbot\Framework\Impl;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Blueprint\Conversation\RunningSpy;
use Commune\Chatbot\Contracts\EventDispatcher;
use Commune\Chatbot\Framework\Conversation\RunningSpyTrait;
use Commune\Chatbot\Framework\Exceptions\ChatbotLogicException;
use Commune\Support\Uuid\HasIdGenerator;
use Commune\Support\Uuid\IdGeneratorHelper;
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



    public function dispatch(object $event, Conversation $conversation = null)
    {
        $this->conversation = $conversation;
        $eventName = get_class($event);
        $eventObject = new GenericEvent($event);
        $result = $this->dispatcher->dispatch($eventName, $eventObject);
        $this->conversation = null;
        return $result;
    }


    public function listen(string $eventName, $listener) : void
    {
        // 用一个类进行监听.
        if (is_string($listener) && class_exists($listener)) {
            $this->listenClassMethod($eventName, [$listener, '__invoke']);

        // 用数组的方式来监听.
        } elseif ( is_array($listener)) {
            $this->listenClassMethod($eventName, $listener);

        // 用callable 方式监听.
        } elseif( is_callable($listener)) {
            $this->listenCallable($eventName, $listener);

        } else {
            throw new ChatbotLogicException(
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
                ) use ($caller) {
                    $this->call(
                        $caller,
                        $event,
                        $eventName,
                        $dispatcher
                    );
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
            throw new ChatbotLogicException("register listener to event $eventName with bad definition that class is '$clazz' and method is '$method'");
        }

        $reflection = new \ReflectionClass($clazz);
        if (!$reflection->hasMethod($method)) {
            throw new ChatbotLogicException("register listener to event $eventName with bad definition that class $clazz do not have method $method");
        }

        // 静态方法认为不需要依赖注入.
        if ($reflection->getMethod($method)->isStatic()) {
            $this->listenCallable($eventName, $classAndMethod);
            return;
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
                    $handler = $this->make($eventName, $clazz);
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

    protected function make(string $eventName, string $clazz)
    {
        if (isset($this->conversation)) {
            return $this->conversation->make($clazz);
        }

        throw new ChatbotLogicException("event $eventName listened by $clazz should only be fired with conversation for dependencies injection");
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
        call_user_func($caller, $subject, $eventName,  $dispatcher, $event);
    }

    public function __destruct()
    {
        static::removeRunningTrace($this->id);
    }
}