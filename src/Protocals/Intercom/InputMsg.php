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

use Commune\Protocals\Comprehension;
use Commune\Protocals\HostMsg;
use Commune\Protocals\IntercomMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Comprehension $comprehension
 */
interface InputMsg extends IntercomMsg
{
    /*----- 额外的信息 -----*/

    public function getShellName() : string;

    public function getSceneId() : string;

    public function getEnv() : array;

    public function getComprehension() : Comprehension;

    /*----- methods -----*/


    /**
     * @param HostMsg $message
     * @param int $deliverAt
     * @param string|null $guestId
     * @param string|null $sessionId
     * @param string|null $messageId
     * @return OutputMsg
     */
    public function output(
        HostMsg $message,
        int $deliverAt = 0,
        string $guestId = null,
        string $sessionId = null,
        string $messageId = null
    ) : OutputMsg;


}