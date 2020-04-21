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
     * 平台的名称, 也是对客户端的名称.
     * @return string
     */
    public function getName() : string;

    /**
     * 同一个 Platform 在不同服务器上也会有不同的实例.
     * 所以区别实例的关系, 还需要一个 Platform ID
     * @return string
     */
    public function getId() : string;

    /**
     * 平台的配置
     * @return PlatformConfig
     */
    public function getConfig() : PlatformConfig;

    /*----- 运行 -----*/

    /**
     * 运行 Platform
     */
    public function start() : void;

    /*----- 内部命令 -----*/

    /**
     * 从系统内部通过逻辑指令来关闭整个平台.
     * 例如: 命令行对话机器人, shutdown 就直接退出程序.
     */
    public function shutdown() : void;

}