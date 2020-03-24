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

use Commune\Chatbot\Contracts\Cache;
use Commune\Container\ContainerContract;
use Commune\Framework\Contracts\ConsoleLogger;
use Commune\Framework\Contracts\ExceptionReporter;
use Commune\Framework\Contracts\Server;
use Commune\Framework\Contracts\ServiceProvider;
use Psr\Log\LoggerInterface;

/**
 * 应用. 可以是 Shell, 或者 Ghost
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface App
{

    /**
     * 获取服务端实例.
     * @return Server
     */
    public function getServer() : Server;

    /**
     * 不一定是单例.
     * @return Cache
     */
    public function getCache() : Cache;

    /*----------- 容器 -----------*/

    /**
     * 请求级容器
     * @return ReqContainer
     */
    public function getReqContainer() : ReqContainer;

    /**
     * 进程级容器.
     * @return ContainerContract
     */
    public function getProcContainer() : ContainerContract;

    /*----------- 服务 -----------*/

    /**
     * 注册服务
     * @param string|ServiceProvider $serviceProvider
     * @param bool $top
     */
    public function register($serviceProvider, bool $top = false) : void;

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
     * 异常通报机制.
     * @return ExceptionReporter
     */
    public function getExceptionReporter() : ExceptionReporter;

    /**
     * 控制台输出.
     * @return ConsoleLogger
     */
    public function getConsoleLogger() : ConsoleLogger;

}
