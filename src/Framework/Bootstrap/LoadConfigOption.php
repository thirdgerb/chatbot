<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Bootstrap;

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\Bootstrapper;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\LogInfo;
use Commune\Support\Option\Option;


/**
 * 读取配置文件中所有预定义的配置.
 * 直接作为实例绑定到 worker process 容器上.
 *
 * 已经加载的配置是高优先级的
 * 后续的组件注册时只能看是否已经绑定过.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class LoadConfigOption implements Bootstrapper
{

    public function bootstrap(App $app) : void
    {
        $container = $app->getProcContainer();
        $logger = $app->getConsoleLogger();
        $logInfo = $app->getLogInfo();

        $configBindings = $this->getConfigOptions($app);

        // 绑定所有用类来标记的配置数据. 方便依赖注入.
        foreach($configBindings as $key => $value) {

            $this->registerAndValidateEntry(
                $logger,
                $logInfo,
                $container,
                $key,
                $value
            );
        }
    }

    abstract protected function getConfigOptions(App $app) : array;

    /**
     * 注册并检查一个配置.
     * 检查配置只在 debug 状态下.
     *
     * @param ConsoleLogger $logger
     * @param LogInfo $logInfo
     * @param ContainerContract $container
     * @param $key
     * @param $value
     */
    protected function registerAndValidateEntry(
        ConsoleLogger $logger,
        LogInfo $logInfo,
        ContainerContract $container,
        $key,
        $value
    ) : void
    {
        // 配置的 name
        $name = is_string($key) ? $key : $value;

        // 判断chatbotName 是否合法.
        if (!is_string($name) || !is_a($name, Option::class, TRUE)) {
            throw new InvalidArgumentException(
                static::class . '::' . __FUNCTION__,
                 'optionName',
                 ' config name ' . $name . ' must be instanceof ' . Option::class
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
            $container->instance($name, $o = new $value);
            $container->instance($value, $o);

            // 闭包的话, 还是走绑定逻辑.
        } elseif ($value instanceof \Closure) {
            $container->singleton($name, $value);

        } else {
            throw new InvalidArgumentException(

                static::class . '::' . __FUNCTION__,
                'option value',
                ' config value of ' . $name . ' is not valid : ' . var_export($value, true)
            );
        }

        $logger->debug($logInfo->bootingRegisterConfigOption($name));
    }
}