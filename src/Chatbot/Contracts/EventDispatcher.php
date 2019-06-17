<?php

/**
 * Class EventDispatcher
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;

use Symfony\Component\EventDispatcher\Event;

/**
 * 给chatbot 使用的事件组件.
 * 默认对Symfony 进行了封装, 使之可以按需使用依赖注入.
 * @see \Symfony\Component\EventDispatcher\EventDispatcherInterface
 *
 * Interface EventDispatcher
 * @package Commune\Chatbot\Contracts
 *
 */
interface EventDispatcher
{
    /**
     * Dispatches an event to all registered listeners.
     *
     * @param string     $eventName The name of the event to dispatch. The name of
     *                              the event is the name of the method that is
     *                              invoked on listeners.
     * @param Event|null $event     The event to pass to the event handlers/listeners
     *                              If not supplied, an empty Event instance is created
     *
     * 返回情况要记录日志?
     */
    public function dispatchByName(string $eventName, Event $event = null) : void;


    public function dispatch(Event $event) : void;

    /**
     * @param string $eventName
     * @param string|callable $listener
     */
    public function listen(string $eventName, $listener) : void;

    //todo doc
    public function listenCallable(
        string $eventName,
        callable $caller,
        int $priority = 0
    ) : void;



    /**
     * @param string $eventName
     * @param string $clazzOrMethod  "clazz@method#priority"
     */
    public function listenClass(
        string $eventName,
        string $clazzOrMethod
    ) : void;

}