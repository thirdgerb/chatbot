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
use Commune\Blueprint\Kernel\Protocols\GhostResponse;

/**
 * Shell 发送消息给 Ghost 的模块.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellMessenger
{
    /**
     * 发送一个同步请求到 Ghost
     *
     * @param GhostRequest $request
     * @return GhostResponse
     */
    public function sendGhostRequest(GhostRequest $request) : GhostResponse;

}