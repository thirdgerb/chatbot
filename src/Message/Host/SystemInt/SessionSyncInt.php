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
 * 告知 shell 变更对应 ghost 的 session, 从而与目标 session 同步.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $session
 */
class SessionSyncInt extends IIntentMsg
{
    const DEFAULT_LEVEL = HostMsg::DEBUG;
    const INTENT_NAME = HostMsg\DefaultIntents::SYSTEM_SESSION_SYNC;

    public static function instance(string $session) : self
    {
        $slots = ['session' => $session];
        return new static($slots);
    }

    public static function intentStub(): array
    {
        return [
            'session' => '',
        ];
    }

}