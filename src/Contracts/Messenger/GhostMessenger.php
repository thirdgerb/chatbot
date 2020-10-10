<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts\Messenger;

use Commune\Blueprint\Kernel\Protocols\GhostRequest;

/**
 * Ghost 转发异步输入消息的模块.
 *
 * 通常用于 Ghost 内部. 因为 Shell 的异步消息直接投递给端口就可以了.
 *
 * 对于 Ghost 而言, 异步消息相比同步消息有两个关键区别:
 *
 * 1. 当 Clone 上锁时, 同步消息会回复说服务器忙, 而异步消息则等待解锁. (要解决循环检查锁的问题)
 * 2. 异步请求的消息可以不直接发送给 Shell, 而是通过广播的方式.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostMessenger
{

    /**
     * 发送异步输入消息. 可以是管道或者是其它.
     * 这个环节没有设返回值, 比较适合用协程 Task 来实现.
     * 不必关心返回值.
     *
     * @param GhostRequest $request
     * @param GhostRequest[] $requests
     */
    public function asyncSendRequest(GhostRequest $request, GhostRequest ...$requests) : void;

    /**
     * @return GhostRequest|null
     */
    public function receiveAsyncRequest() : ? GhostRequest;
}