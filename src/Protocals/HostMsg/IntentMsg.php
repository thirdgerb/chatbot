<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\HostMsg;

use Commune\Protocals\HostMsg;

/**
 * Ghost 对外发表的响应意图.
 * 通常会被 Renderer 解析成多个其它类型的 HostMsg
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface IntentMsg extends HostMsg
{
    /*----- system intents -----*/

    const SYSTEM_SESSION_BUSY = 'intents.system.session.busy';
    const SYSTEM_SESSION_QUIT = 'intents.system.session.quit';
    const SYSTEM_REQUEST_FAILURE = 'intents.system.request.fail';


    /*----- guest intents -----*/


    public function getIntentName() : string;

    public function getSlots() : array;

}