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

use Commune\Protocals\Intercom\GhostInput;
use Commune\Protocals\Intercom\GhostMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface MessageDB
{

    /**
     * 记录一个 Input 消息.
     * @param GhostInput $ghostInput
     * @return bool
     */
    public function recordInput(GhostInput $ghostInput) : bool;

    /**
     * 记录一个消息
     * @param GhostMsg $message
     * @return bool
     */
    public function record(GhostMsg $message) : bool;

    /**
     * @param callable $fetcher
     * @return GhostMsg[]
     */
    public function fetch(callable $fetcher) : array;


    /**
     * 按条件获取若干消息
     * @return Condition
     */
    public function where() : Condition;

    /**
     * 获取消息.
     * @param string $messageId
     * @return GhostMsg|null
     */
    public function find(string $messageId) : ? GhostMsg;
}