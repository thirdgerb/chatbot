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

use Commune\Protocals\HostMsg;
use Commune\Protocals\IntercomMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface InputMsg extends IntercomMsg
{

    /**
     * @param string $sessionId
     */
    public function setSessionId(string $sessionId) : void;


    /**
     * 基于 Input, 输出一个 output
     * @param HostMsg $message
     * @param string $creatorId
     * @param string $creatorName
     * @param int|null $deliverAt
     * @param string|null $sessionId
     * @param string|null $scene
     * @param bool $fromBot
     * @return OutputMsg
     */
    public function output(
        HostMsg $message,
        string $creatorId = '',
        string $creatorName = '',
        int $deliverAt = null,
        string $sessionId = null,
        string $scene = null,
        bool $fromBot = true
    ) : OutputMsg;

}