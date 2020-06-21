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


    public function output(
        HostMsg $message,
        string $creatorId = '',
        string $creatorName = '',
        int $deliverAt = 0
    ) : OutputMsg;

}