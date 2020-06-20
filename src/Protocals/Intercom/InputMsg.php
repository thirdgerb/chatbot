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
    const ENV_KEY_QUERY = 'query';

    /*----- 额外的信息 -----*/

    /**
     * @return string
     */
    public function getScene() : string;

    /**
     * @return array
     */
    public function getEnv() : array;

    /**
     * @return Comprehension
     */
    public function getComprehension() : Comprehension;

    /*----- setter -----*/

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
     * @param string|null $messageId
     * @return OutputMsg
     */
    public function output(
        HostMsg $message,
        int $deliverAt = 0,
        string $shellName = null,
        string $sessionId = null,
        string $guestId = null,
        string $messageId = null
    ) : OutputMsg;


    /**
     * 去掉 Comprehend, Env 等信息后的请求.
     * @return InputMsg
     */
    public function asBareIntercom() : InputMsg;

}