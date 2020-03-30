<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint;

use Commune\Framework\Contracts\Cache;
use Commune\Container\ContainerContract;
use Commune\Framework\Contracts\ConsoleLogger;
use Commune\Framework\Contracts\ExceptionReporter;
use Commune\Framework\Contracts\LogInfo;
use Commune\Framework\Contracts\Messenger;
use Commune\Framework\Contracts\Server;
use Commune\Framework\Contracts\ServiceProvider;
use Commune\Framework\Exceptions\BootingException;
use Psr\Log\LoggerInterface;

/**
 * 应用实例. 可以是 Shell, 或者 Ghost
 * 通常是进程级单例.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ChatApp
{
    /**
     * 是否调试状态.
     * @return bool
     */
    public function isDebugging() : bool;

    /*----------- 必要组件 -----------*/

    /**
     * 获取服务端实例. 通常是单例.
     * @return Server
     */
    public function getServer() : Server;

    /**
     * 获取缓存实例. 通常不是单例, 每次重新生成.
     * @return Cache
     */
    public function getCache() : Cache;

    /**
     * 与 Ghost 的通讯模块
     * 通常不是单例.
     * @return Messenger
     */
    public function getMessenger() : Messenger;

    /**
     * 异常通报机制.
     * 通常不是单例.
     * @return ExceptionReporter
     */
    public function getExceptionReporter() : ExceptionReporter;


    /*----------- 容器 -----------*/

    /**
     * 请求级容器
     * @return ReqContainer
     */
    public function getReqContainer() : ReqContainer;

    /**
     * 创建一个请求级容器, 并添加默认的绑定
     * @param string $id
     * @return ReqContainer
     */
    public function newReqContainerInstance(string $id) : ReqContainer;

    /**
     * 进程级容器.
     * @return ContainerContract
     */
    public function getProcContainer() : ContainerContract;

    /*----------- 服务 -----------*/

    /**
     * @param string $serviceProvider
     * @param array $data
     * @param bool $top
     * @throws BootingException
     */
    public function registerProvider(
        string $serviceProvider,
        array $data = [],
        bool $top = false
    ) : void;

    /**
     * @param ServiceProvider $provider
     * @param bool $top
     */
    public function registerProviderIns(
        ServiceProvider $provider,
        bool $top
    ) : void;

    /**
     * @return ServiceProvider[] string => ServiceProvider
     */
    public function getProcProviders() : array;

    /**
     * @return ServiceProvider[] string => ServiceProvider
     */
    public function getReqProviders() : array;

    /*----------- 初始化流程 -----------*/

    /**
     * 初始化.
     * @throws BootingException
     */
    public function bootstrap() : void;

    /**
     * 初始化进程级服务
     * 进程只执行一次
     */
    public function bootProcServices() : void;

    /**
     * 初始化请求级服务.
     * 每个请求都应该执行一次.
     *
     * @param ReqContainer $container
     */
    public function bootReqServices(ReqContainer $container) : void;

    /*----------- 日志 -----------*/

    /**
     * 进程级的日志单例. 通常在启动时使用.
     * @return LoggerInterface
     */
    public function getLogger() : LoggerInterface;

    /**
     * 日志文本.
     * @return LogInfo
     */
    public function getLogInfo() : LogInfo;

    /**
     * 控制台输出.
     */
    public function getConsoleLogger() : ConsoleLogger;

}
