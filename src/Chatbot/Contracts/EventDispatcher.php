<?php

/**
 * Class EventDispatcher
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;

use Commune\Chatbot\Blueprint\Conversation\Conversation;

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
     * 注册时会根据情况做依赖注入的准备
     * 可以用三种 listener 方式来注册:
     *
     * 1. 类名. 表示 listener 要通过 conversation->make 来依赖注入, 实例化.
     * 2. [类名, 动态方法名], 表示要 conversation->make 依赖注入实例化, 然后调用动态方法.
     * 3. callable 对象, 包括静态方法.  表示和 conversation 上下文无关, 不会用依赖注入.
     *
     * listener 方法的入参为:
     *  -   object $event
     *  -   string $eventName
     *  -   SymfonyDispatcher $dispatcher
     *  -   GenericEvent $generic
     *
     * 可以按顺序选择 1 ~ 4 个.
     *
     * @param string $eventName
     * @param string|array|callable $listener  className, callable
     */
    public function listen(string $eventName, $listener) : void;

    /**
     *
     * 在CommuneChatbot 中注册事件都是请求相关的.
     * 所以需要传入 conversation对象. 否则无法触发.
     * 如果有其它生命周期管理.
     *
     * Provide all relevant listeners with an event to process.
     *
     * @param \object $event
     *   The object to process.
     * @param Conversation|null $conversation;
     *
     * @return null | \object
     *   The Event that was passed, now modified by listeners.
     */
    public function dispatch(object $event, Conversation $conversation = null);

}