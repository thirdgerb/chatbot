<?php

/**
 * Class EventServiceProvider
 * @package Commune\Chatbot\Framework\Providers
 */

namespace Commune\Chatbot\Framework\Providers;

use Commune\Chatbot\Contracts\EventDispatcher;
use Commune\Chatbot\Framework\Predefined\SymfonyEventDispatcher;

class EventServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    protected $events = [];

    protected $booted = false;

    /**
     * @param \Commune\Container\ContainerContract $app
     */
    public function boot($app): void
    {
        if ($this->booted) {
            return;
        }

        /**
         * @var EventDispatcher $dispatcher
         */
        $dispatcher = $app->make(EventDispatcher::class);

        foreach ($this->events as $event => $listeners) {
            // 注册listeners
            foreach ($listeners as $listener) {
                $dispatcher->listen($event, $listener);
            }
        }

        $this->booted = true;
    }

    public function register(): void
    {
        if ($this->app->bound(EventDispatcher::class)) {
            return;
        }

        // event dispatcher 绑定到 conversation 容器
        // 所有请求共享同一个实例.
        // conversation->fire() 方法会替换conversation 实例并生成 listener
        // 这时由请求级别的 conversation 容器负责.
        // 这么做的好处是, listener 进行依赖注入的时候,
        // 注入的都是当前请求内的实例. 从而实现相互隔离.
        $this->app->instance(EventDispatcher::class, new SymfonyEventDispatcher());


    }

}