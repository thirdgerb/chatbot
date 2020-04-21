<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Platform;


/**
 * 服务端实例. 通常对应一个 Worker 进程.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Server
{
    /**
     * Server 的 ID
     * @return string
     */
    public function getId() : string;

    /*---------- 可以被逻辑调用的通讯管理 ---------*/

    /**
     * 关闭 ClientId. 场景:
     * 双工的对话机器人, 逻辑需要主动关闭会话时, 需要调用 Close 方法.
     *
     * @param string $clientId
     */
    public function close(string $clientId) : void;

    /**
     * 非阻塞地休眠. 如果可以做到的话.
     * @param float $seconds
     */
    public function sleep(float $seconds) : void;

    /**
     * server 自己决定未知异常怎么处理. 不需要开发者瞎折腾.
     * @param \Throwable $e
     */
    public function catchExp(\Throwable $e) : void;

}