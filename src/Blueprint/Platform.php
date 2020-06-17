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
use Commune\Blueprint\Framework\App;

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
    const KEY_CONFIG = 'config';

    /**
     * 运行 Platform
     */
    public function serve() : void;


    /**
     * @param float $seconds
     */
    public function sleep(float $seconds) : void;

    /*----- 内部命令 -----*/

    /**
     * 从系统内部通过逻辑指令来关闭整个平台.
     * 例如: 命令行对话机器人, shutdown 就直接退出程序.
     */
    public function shutdown() : void;


    public function catchExp(\Throwable $e) : void;
}