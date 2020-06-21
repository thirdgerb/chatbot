<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\SystemInt;

use Commune\Message\Host\IIntentMsg;
use Commune\Protocals\HostMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SessionBusyInt extends IIntentMsg
{
    const DEFAULT_LEVEL = HostMsg::WARNING;
    const INTENT_NAME = HostMsg\DefaultIntents::SYSTEM_SESSION_BUSY;

    public static function instance() : self
    {
        return new static();
    }
}