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
use Commune\Protocols\HostMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $ucl
 */
class SessionQuitInt extends IIntentMsg
{
    const DEFAULT_LEVEL = HostMsg::INFO;
    const INTENT_NAME = HostMsg\DefaultIntents::SYSTEM_SESSION_QUIT;

    public static function instance(string $ucl = '') : self
    {
        return new static(get_defined_vars());
    }

    public static function intentStub(): array
    {
        return [
            'ucl' => ''
        ];
    }

}