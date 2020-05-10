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

/**
 * Ghost 的输入消息.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 *
 * @property-read Comprehension $comprehension
 */
interface GhostInput extends GhostMsg
{

    /*----- 额外的信息 -----*/

    public function getSessionId() : ? string;

    public function getSenderName() : string;

    public function getSceneId() : string;

    public function getEnv() : array;

    public function getComprehension() : Comprehension;


    /*----- methods -----*/

    public function output(
        HostMsg $message,
        float $deliverAt = 0,
        string $cloneId = null,
        string $shellName = null,
        string $guestId = null
    ) : GhostOutput;

    /**
     * @param string $sessionId
     * @return static
     */
    public function withSessionId(string $sessionId) : GhostInput;


}