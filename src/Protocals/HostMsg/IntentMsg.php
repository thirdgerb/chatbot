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

    const SYSTEM_SESSION_BUSY = 'intent.system.session.busy';
    const SYSTEM_SESSION_QUIT = 'intent.system.session.quit';
    const SYSTEM_SESSION_FAIL = 'intent.system.session.fail';
    const SYSTEM_REQUEST_FAILURE = 'intent.system.request.fail';
    
    const SYSTEM_COMMAND_ERROR = 'intent.system.command.error';
    const SYSTEM_COMMAND_LIST = 'intent.system.command.list';
    const SYSTEM_COMMAND_MISS = 'intent.system.command.miss';
    const SYSTEM_COMMAND_DESC = 'intent.system.command.desc';

    const SYSTEM_DIALOG_BUSY = 'intent.system.dialog.busy';
    const SYSTEM_DIALOG_CONFUSE = 'intent.system.dialog.confuse';

    /*----- guest intents -----*/

    const GUEST_DIALOG_ORDINAL = 'dialogue.ordinal';

    public function getIntentName() : string;

    public function getSlots() : array;

}