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
 * @property-read string $args
 * @property-read string $options
 */
class CommandDescInt extends IIntentMsg
{
    public function __construct(
        string $commandName = '',
        string $args = '',
        string $options = ''
    )
    {
        parent::__construct(
            SystemIntents::COMMAND_DESC,
            [
                'commandName' => $commandName,
                'args' => $args,
                'opts' => $options
            ],
            MsgLevel::INFO
        );
    }

}