<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype\SystemInts;

use Commune\Message\Blueprint\Tag\MsgLevel;
use Commune\Message\Constants\SystemIntents;
use Commune\Message\Prototype\IIntentMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $commandName
 */
class CommandNotExistsInt extends IIntentMsg
{
    public function __construct(string $commandName = '')
    {
        parent::__construct(
            SystemIntents::COMMAND_NOT_EXISTS,
            [
                'commandName' => $commandName,
            ],
            MsgLevel::ERROR
        );
    }

}