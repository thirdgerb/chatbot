<?php

/**
 * Class ChatbotServer
 * @package Commune\Chatbot\Contracts
 */

namespace Commune\Chatbot\Contracts;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Blueprint\Conversation\Conversation;

/**
 * 端上的 server
 * 主要是 close, closeClient 对kernel的流程很重要.
 *
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
     * 关闭整个 server
     */
    public function fail() : void;

    /**
     * 关闭一个 server 的客户端.
     *
     * @param Conversation $conversation
     */
    public function closeClient(Conversation $conversation) : void;

}