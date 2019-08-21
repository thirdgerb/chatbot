<?php

/**
 * Class EventServiceProvider
 * @package Commune\Chatbot\Framework\Providers
 */

namespace Commune\Chatbot\Framework\Providers;

use Commune\Chatbot\Config\ChatbotConfig;
use Commune\Chatbot\Contracts\EventDispatcher;
use Commune\Chatbot\Framework\Predefined\SymfonyEventDispatcher;
use Psr\Log\LoggerInterface;

class EventServiceProvider extends BaseServiceProvider
{

    /**
     * @param \Commune\Container\ContainerContract $app
     */
    public function boot($app): void
    {
        /**
         * @var ChatbotConfig $config
         */
        $config = $app->make(ChatbotConfig::class);
        /**
         * @var EventDispatcher $dispatcher
         */
        $dispatcher = $app->make(EventDispatcher::class);

        if (isset($config) && isset($config->eventRegister)) {

            // 默认用config 来取配置.
            foreach ($config->eventRegister as $eventListenerConfig) {
                $name = $eventListenerConfig->event;
                // 注册listeners
                foreach ($eventListenerConfig->listeners as $listener) {
                    $dispatcher->listen($name, $listener);
                }

            }
        }
    }

    public function register(): void
    {
        // event dispatcher 绑定到conversation 容器
        // 每个conversation 启动时加载 listener
        // 这么做的好处是, listener 进行依赖注入的时候,
        // 注入的都是当前请求内的实例. 从而实现相互隔离.
        $this->app
            ->singleton(
                EventDispatcher::class,
                function($app) {
                    return new SymfonyEventDispatcher(
                        $app,
                        $app[LoggerInterface::class]
                    );
                }
            );
    }

}