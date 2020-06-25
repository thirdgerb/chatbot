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

use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Framework\ProcContainer;
use Psr\Log\LoggerInterface;

/**
 * 平台是 Host 在服务器上启动的服务端程序.
 * 一个异构的 Host 可能在不同服务器上有多种 Platform
 * 每个 Platform, 例如 wechat 端, 也可能是分布式部署的.
 *
 * PlatformConfig 是 Platform 的抽象定义.
 * 这里的 Platform 是一个实例.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Platform
{

    /**
     * @return string
     */
    public function getId() : string;

    /**
     * 获取运行在 Platform 上的 App Id. 通常是 ShellId 或者 GhostId
     * @return string
     */
    public function getAppId() : string;

    /**
     * @return PlatformConfig
     */
    public function getConfig() : PlatformConfig;

    /**
     * @return ProcContainer
     */
    public function getContainer() : ProcContainer;

    /**
     * @return LoggerInterface
     */
    public function getLogger() : LoggerInterface;

    /**
     * 运行 Platform
     */
    public function serve() : void;

    /*----- 内部命令 -----*/

    /**
     * @param float $seconds
     */
    public function sleep(float $seconds) : void;


    /**
     * 从系统内部通过逻辑指令来关闭整个平台.
     * 例如: 命令行对话机器人, shutdown 就直接退出程序.
     */
    public function shutdown() : void;


    /**
     * @param \Throwable $e
     */
    public function catchExp(\Throwable $e) : void;
}