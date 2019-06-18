<?php

/**
 * Class LoadConfiguration
 * @package Commune\Chatbot\Framework\Bootstrap
 */

namespace Commune\Chatbot\Framework\Bootstrap;


use Schematic\Entry;
use Psr\Log\LoggerInterface;
use Commune\Container\ContainerContract;
use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;

/**
 * 读取配置文件中所有预定义的配置.
 * 直接作为实例绑定到 Reactor 容器上.
 *
 * 已经加载的配置是高优先级的
 * 后续的组件注册时只能看是否已经绑定过.
 *
 * Class LoadConfiguration
 * @package Commune\Chatbot\Framework\Bootstrap
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class LoadConfiguration implements Bootstrapper
{
    public function bootstrap(Application $app) : void
    {
        $container = $app->getReactorContainer();
        $logger = $app->getConsoleLogger();

        // chatbot config
        $chatbotConfig = $app->getConfig();

        // 绑定所有用类来标记的配置数据. 方便依赖注入.
        foreach($chatbotConfig->configBindings as $key => $value) {

            $this->registerAndValidateEntry(
                $logger,
                $container,
                $key,
                $value
            );
        }
    }

    /**
     * 注册并检查一个配置.
     * 检查配置只在 debug 状态下.
     *
     * @param LoggerInterface $logger
     * @param ContainerContract $container
     * @param mixed $key
     * @param mixed $value
     */
    protected function registerAndValidateEntry(
        LoggerInterface $logger,
        ContainerContract $container,
        $key,
        $value
    ) : void
    {
        // 配置的 name
        $name = is_string($key) ? $key : $value;

        // 判断chatbotName 是否合法.
        if (!is_string($name) || !is_a($name, Entry::class, TRUE)) {
            //todo
            throw new ConfigureException(
                static::class
                . ' config name '
                . $name
                . ' must be instanceof Entry'
            );
        }

        if (is_array($value)) {
            $container->instance($name, new $name($value));

        } elseif (
            // 实现是字符串
            is_string($value)
            && (
                // 两个类名相等.
                $name === $value
                // concrete 必须是 name 的子类
                || is_a($value, $name, TRUE)
            )
        ) {
            $container->instance($name, new $value);

        // 闭包的话, 还是走绑定逻辑.
        } elseif ($value instanceof \Closure) {
            $container->singleton($name, $value);

        } else {
            throw new ConfigureException(
                static::class
                . ' config value of '
                . $name
                . ' is not valid : '
                . var_export($value, true)
            );
        }

        $logger->debug("register config entry, name: $name");
    }
}