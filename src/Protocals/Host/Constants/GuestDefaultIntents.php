<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Host\Constants;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GuestDefaultIntents
{
    /*------- memory -------*/

    // 交换一个记忆, 通常是内部意图. 可以让 Ghost 和 Shell 交换信息
    const RECOLLECTION_EXCHANGE = 'system.memory.exchange';

    // 索取一个记忆. 可以是双向的意图.
    const RECOLLECTION_QUERY = 'system.memory.query';

    /*------- messenger -------*/

    const INTERCOM_FAILURE = 'system.intercom.failure';

    /*------- chat -------*/

    const CHAT_BLOCKED = 'system.chat.blocked';

    /*------- command -------*/

    const COMMAND_INVALID = 'system.command.invalid';

    const COMMAND_NOT_EXISTS = 'system.command.notExists';

    const COMMAND_DESC = 'system.command.desc';

}