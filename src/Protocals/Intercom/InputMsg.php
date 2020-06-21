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
     * @param string $sceneId
     */
    public function setSceneId(string $sceneId) : void;

    /*----- methods -----*/

    /**
     * @param HostMsg $message
     * @param int $deliverAt
     * @param string|null $shellName
     * @param string|null $sessionId
     * @param string|null $guestId
     * @return OutputMsg
     */
    public function output(
        HostMsg $message,
        int $deliverAt = 0,
        string $shellName = null,
        string $sessionId = null,
        string $guestId = null
    ) : OutputMsg;

}