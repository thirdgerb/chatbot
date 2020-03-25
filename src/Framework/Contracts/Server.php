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

use Commune\Framework\Blueprint\App;

/**
 * 服务端实例.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Server
{
    public function getApp() : App;

    /*---------- 运行相关 ---------*/

    /**
     * 启动服务端实例.
     */
    public function start() : void;

    /**
     * 尝试关闭服务端实例. 根据服务端情况.
     */
    public function shutdown() : void;

    /**
     * 重启服务端实例. 办得到吗?
     */
    public function reboot() : void;

    /*---------- 特殊操作 ---------*/

    /**
     * 非阻塞地休眠. 如果可以做到的话.
     * @param float $seconds
     */
    public function coSleep(float $seconds) : void;

    /**
     * 执行一个异步任务, 如果允许的话.
     * 否则当同步任务执行也行.
     * 具体细节, 本项目暂时不考虑了.
     *
     * @param string $id
     * @param array $payload
     */
    public function job(string $id, array $payload) : void;

    /**
     * server 自己决定未知异常怎么处理. 不需要开发者瞎折腾.
     * @param \Throwable $e
     */
    public function catchExp(\Throwable $e) : void;

}