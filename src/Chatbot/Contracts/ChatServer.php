<?php

namespace Commune\Chatbot\Contracts;


use Commune\Chatbot\Blueprint\Conversation\Conversation;

/**
 * 端上的 server
 * 负责让业务逻辑直接操作服务端.
 * 常用功能是 close, closeClient 对kernel的流程很重要.
 */
interface ChatServer
{

    /**
     * 运行server
     */
    public function run() : void;

    /**
     * server 允许的 sleep 方式.
     * 比如swoole 应该用非阻塞的sleep
     * @param int $millisecond
     */
    public function sleep(int $millisecond) : void;

    /**
     * 决定 Server 是否能响应. 所有的 Server 都受影响.
     * @return bool
     */
    public function isAvailable() : bool;

    /**
     * 设定 Server 是否能响应. 所有的 Server 都受影响.
     * @param bool $boolean
     */
    public function setAvailable(bool $boolean) : void;

    /**
     * 关闭当前 server.
     */
    public function fail() : void;

    /**
     * 关闭一个 server 的客户端. 允许重新连接.
     *
     * @param Conversation $conversation
     */
    public function closeClient(Conversation $conversation) : void;

}