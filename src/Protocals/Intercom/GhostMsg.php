<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Intercom;

use Commune\Protocals\IntercomMsg;

/**
 * Ghost 输入和输出时的消息.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostMsg extends IntercomMsg
{
    /*---- ghost info ----*/

    /**
     * Ghost 分身的 Id. 默认是 ShellId
     * @return string
     */
    public function getCloneId() : string;

    /**
     * 用户的 身份Id, 默认是 SenderId
     * @return string
     */
    public function getGuestId() : string;

    public function getShellId() : string;

    public function getShellName() : string;

    public function getSenderId() : string;

    public function getMessageId(): string;

    public function getBatchId() : string;

    /*---- status ----*/

    /**
     * 是否属于需要广播的消息.
     * @return bool
     */
    public function isBroadcasting() : bool;

    public function toShellMsg() : ShellMsg;

    public function withSessionId(string $sessionId) : GhostMsg;
}