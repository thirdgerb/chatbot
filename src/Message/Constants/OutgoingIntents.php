<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Constants;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface OutgoingIntents
{
    /*------- messenger -------*/

    const INTERCOM_FAILURE = 'system.intercom.failure';

    const CHAT_BLOCKED = 'system.chat.blocked';

    /*------- command -------*/

    const COMMAND_INVALID = 'system.command.invalid';

    const COMMAND_NOT_EXISTS = 'system.command.notExists';

    const COMMAND_DESC = 'system.command.desc';

}