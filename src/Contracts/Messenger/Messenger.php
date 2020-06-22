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

use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Messenger
{
    /**
     * 发送一个同步请求到
     *
     * @param GhostRequest $request
     * @return GhostResponse
     */
    public function sendInput2Ghost(GhostRequest $request) : GhostResponse;

    /**
     * 发送异步输入消息. 可以是管道或者是其它.
     * 这个环节没有设返回值, 比较适合用协程 Task 来实现.
     * 不必关心返回值.
     *
     * @param GhostRequest $request
     * @param GhostRequest ...$requests
     */
    public function asyncSend2Ghost(GhostRequest $request, GhostRequest ...$requests) : void;

    /**
     * @return GhostRequest|null
     */
    public function popAsyncGhostRequest() : ? GhostRequest;
}