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
 *
 * @property-read string $command
 */
class CommandMissInt extends IIntentMsg
{
    public function __construct(string $command)
    {
        parent::__construct(
            HostMsg\IntentMsg::SYSTEM_COMMAND_MISS,
            [
                'command' => $command,
            ],
            HostMsg::ERROR
        );
    }

}