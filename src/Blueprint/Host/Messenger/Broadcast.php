<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Host\Messenger;

use Commune\Protocals\Intercom\GhostMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Broadcast
{
    /**
     * 广播一个 Ghost 的消息.
     * @param GhostMsg $message
     * @return bool
     */
    public function publish(GhostMsg $message) : bool;

    /**
     * 监听 ghost 推送的消息.
     *
     * @param callable $action
     * @param string|null $cloneId
     */
    public function subscribe(callable $action, string $cloneId = null) : void;

}