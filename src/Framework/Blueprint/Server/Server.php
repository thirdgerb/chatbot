<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Server;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Server
{

    /**
     * 服务端实例的 ID, 方便排查问题.
     * @return string
     */
    public function getId() : string;

    /*---------- 运行相关 ---------*/

    /**
     * 启动服务端实例.
     */
    public function start() : void;

    /*---------- 特殊操作 ---------*/

    /**
     * 关闭客户端. 如果是长连接的话尤其.
     * @param string $chatId
     */
    public function closeClient(string $chatId) : void;

    /**
     * 非阻塞地休眠. 如果可以做到的话.
     * @param float $seconds
     */
    public function coSleep(float $seconds) : void;

    /**
     * server 自己决定未知异常怎么处理. 不需要开发者瞎折腾.
     * @param \Throwable $e
     */
    public function catchExp(\Throwable $e) : void;
}