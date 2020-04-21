<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Platform\Ghost;

use Commune\Protocals\Intercom\GhostMsg;


/**
 * 发送 Ghost 端响应.
 * Ghost 通常是同步逻辑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostRes
{

    /**
     * @return GhostMsg[]
     */
    public function getOutputs() : array;

    /**
     * @param GhostMsg[] $messages
     */
    public function setOutputs(array $messages) : void;

    public function append(GhostMsg $message) : void;

    public function send() : bool;
}