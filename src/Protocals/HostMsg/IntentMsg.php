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

    const SYSTEM_SESSION_BUSY = 'system.session.busy';
    const SYSTEM_SESSION_QUIT = 'system.session.quit';
    const SYSTEM_SESSION_FAIL = 'system.session.fail';
    const SYSTEM_REQUEST_FAILURE = 'system.request.fail';
    
    const SYSTEM_COMMAND_ERROR = 'system.command.error';
    const SYSTEM_COMMAND_LIST = 'system.command.list';
    const SYSTEM_COMMAND_MISS = 'system.command.miss';
    const SYSTEM_COMMAND_DESC = 'system.command.desc';

    const SYSTEM_DIALOG_YIELD = 'system.dialog.yield';
    const SYSTEM_DIALOG_CONFUSE = 'system.dialog.confuse';
    const SYSTEM_DIALOG_ASK = 'system.dialog.ask';

    /*----- guest intents -----*/

    const GUEST_NAVIGATE_CANCEL = 'navigation.cancel';
    const GUEST_NAVIGATE_QUIT = 'navigation.quit';
    const GUEST_NAVIGATE_HOME = 'navigation.home';
    const GUEST_NAVIGATE_BACK = 'navigation.backward';
    const GUEST_NAVIGATE_REPEAT = 'navigation.repeat';
    const GUEST_NAVIGATE_RESTART = 'navigation.restart';

    const GUEST_DIALOG_ORDINAL = 'dialogue.ordinal';

    public function getIntentName() : string;

    public function getSlots() : array;

}