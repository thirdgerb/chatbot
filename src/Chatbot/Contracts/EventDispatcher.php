<?php

/**
 * Class EventDispatcher
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;

use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * 给chatbot 使用的事件组件.
 * 默认对Symfony 进行了封装, 使之可以按需使用依赖注入.
 * @see \Symfony\Component\EventDispatcher\EventDispatcherInterface
 *
 * Interface EventDispatcher
 * @package Commune\Chatbot\Contracts
 *
 */
interface EventDispatcher extends EventDispatcherInterface
{


    /**
     * @param string $eventName
     * @param string|array|callable $listener  className, callable
     */
    public function listen(string $eventName, $listener) : void;

}