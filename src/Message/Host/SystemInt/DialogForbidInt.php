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
 */
class DialogForbidInt extends IIntentMsg
{
    const DEFAULT_LEVEL = HostMsg::INFO;
    const INTENT_NAME = HostMsg\DefaultIntents::SYSTEM_DIALOG_FORBID;

    public static function instance(string $context = '', string $policy = '') : self
    {
        return new static(get_defined_vars());
    }

    public static function intentStub(): array
    {
        return [
            'context' => '',
            'policy' => '',
        ];
    }
}