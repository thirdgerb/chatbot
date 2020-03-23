<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Contracts;

/**
 * 服务端实例.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Server
{
    /**
     * 启动服务端实例.
     */
    public function start() : void;

    /*---------- 特殊操作 ---------*/

    /**
     * 非阻塞地休眠. 如果可以做到的话.
     * @param float $seconds
     */
    public function coSleep(float $seconds) : void;

    /**
     * 尝试关闭服务端实例. 根据服务端情况.
     */
    public function shutdown() : void;

    /**
     * 重启服务端实例. 办得到吗?
     */
    public function reboot() : void;

}