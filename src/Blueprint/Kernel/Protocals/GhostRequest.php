<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Kernel\Protocals;

use Commune\Protocals\HostMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostRequest extends AppRequest
{
    /**
     * 是否是异步消息.
     * @return bool
     */
    public function isAsync() : bool;

    /**
     * 要求极简的回复, 不需要消息体.
     * @return bool
     */
    public function requireTinyResponse() : bool;

    /**
     * @param HostMsg $message
     * @param HostMsg ...$messages
     * @return CloneResponse
     */
    public function output(HostMsg $message, HostMsg ...$messages) : CloneResponse;

    /**
     * @return GhostResponse
     */
    public function noContent() : GhostResponse;

    /**
     * @param int $errcode
     * @param string $errmsg
     * @return GhostResponse
     */
    public function fail(int $errcode, string $errmsg = '') : GhostResponse;
}