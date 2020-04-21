<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint;

use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Configs\ShellConfig;
use Commune\Blueprint\Exceptions\Boot\AppNotBootException;
use Commune\Blueprint\Exceptions\Boot\AppNotDefinedException;
use Commune\Blueprint\Exceptions\Boot\BootRepetitionException;
use Commune\Blueprint\Exceptions\Boot\HostNotRunningException;
use Commune\Blueprint\Host\ServiceRegistrar;
use Commune\Blueprint\Platform\Server;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Blueprint\Configs\HostConfig;
use Commune\Support\Option\Registry;


/**
 * 机器人应用
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Host
{
    /*----- properties -----*/

    /**
     * 机器人应用的名称.
     *
     * 不一定是用户看到的机器人名称.
     * 主要用于隔离各种存储数据, 避免几个机器人共用了缓存等配置结果互相干扰.
     *
     * @return string
     */
    public function getName() : string;

    /**
     * 机器人配置
     * @return HostConfig
     */
    public function getConfig() : HostConfig;

    /**
     * 调试模式
     * @return bool
     */
    public function isDebugging() : bool;

    /*----- 启动 -----*/

    /**
     * 使用 Platform 名称正式启动一个 Platform.
     * 根据 Platform 的配置, 同时启动配置中的 Shell 和 Ghost, 如果有定义的话.
     * @param string $platformName
     * @throws AppNotDefinedException
     */
    public function run(string $platformName) : void;

    /*----- container -----*/

    /**
     * 进程级容器
     * @return ContainerContract
     */
    public function getProcContainer() : ContainerContract;

    /**
     * 请求级容器
     * @return ContainerContract
     */
    public function getReqContainer() : ContainerContract;


    /*----- register -----*/

    /**
     * 获取服务配置注册实例.
     * @return ServiceRegistrar
     */
    public function getServiceRegistrar() : ServiceRegistrar;

    /**
     * 配置中心注册表.
     * @return Registry
     */
    public function getOptionRegistry() : Registry;

    /*----- global singletons 系统全局单例. -----*/

    # 机器人 Host 是服务端程序.
    # 运行时本质是在单一服务器上启动一个 Platform 的实例.
    # 这个实例会启动若干个进程 Server 实例.
    # 每个 Server 实例上会运行 0~1 个 Shell 或者 Ghost 的 App
    #
    # 这四种实例组合构成 Host 的实例, 是异构方案中最基础的抽象.
    #
    # 一个 Host 可能在多个 Platform 上启动. Platform 相互之间进行通讯.
    # 通讯的逻辑落在某一个 Server 上运行, 具体由 shell 或 Ghost App 服务响应逻辑.

    /**
     * 当前 Host 的服务端实例.
     * @return Platform
     * @throws AppNotBootException
     */
    public function getPlatform() : Platform;

    /**
     * 当前 Host 实例的多轮对话管理内核
     * @return Ghost
     * @throws AppNotBootException
     */
    public function getGhost() : Ghost;

    /**
     * 当前 Host 实例负责对输入输出消息进行加工的外壳
     * @return Shell
     * @throws AppNotBootException
     */
    public function getShell() : Shell;

    /**
     * 当前程序运行所在的 Server Worker, 通常是一个进程.
     * 用于管理 Server 本身的一些功能.
     *
     * @return Server
     * @throws HostNotRunningException
     */
    public function getServer() : Server;

    /**
     * @return ConsoleLogger
     * @throws AppNotBootException
     */
    public function getConsoleLogger() : ConsoleLogger;


    /*----- bootstrap -----*/


    /**
     * 是否已经初始化了 Host 实例.
     * @return bool
     */
    public function isBooted() : bool;


    /**
     * 初始化当前的 Host 实体.
     * 通常在 ::run($platform) 时自动调用. 只允许调用一次.
     *
     * 每个 Host 实体只能有 一个 Platform, 一个 Shell, 一个 Ghost
     * 核心是 Platform.
     *
     * @param PlatformConfig $platformConfig
     * @param ShellConfig|null $shellConfig
     * @param GhostConfig|null $ghostConfig
     *
     * @throws BootRepetitionException          不可以重复启动.
     */
    public function bootstrap(
        PlatformConfig $platformConfig,
        ShellConfig $shellConfig = null,
        GhostConfig $ghostConfig = null
    ) : void;

}