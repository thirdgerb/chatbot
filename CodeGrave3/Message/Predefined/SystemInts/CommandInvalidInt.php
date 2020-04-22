<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Predefined\SystemInts;

use Commune\Message\Blueprint\Tag\MsgLevel;
use Commune\Message\Constants\SystemIntents;
use Commune\Message\Predefined\IIntentMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $commandName
 * @property-read string $errorMsg
 */
class CommandInvalidInt extends IIntentMsg
{
    public function __construct(
        string $commandName = '',
        string $errorMsg = ''
    )
    {
        parent::__construct(
            SystemIntents::COMMAND_INVALID,
            [
                'commandName' => $commandName,
                'error' => $errorMsg
            ],
            MsgLevel::ERROR
        );
    }

}