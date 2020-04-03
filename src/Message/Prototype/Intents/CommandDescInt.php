<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype\Intents;

use Commune\Message\Blueprint\Tag\MsgLevel;
use Commune\Message\Constants\OutgoingIntents;
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
        string $args,
        string $options
    )
    {
        parent::__construct(
            OutgoingIntents::COMMAND_DESC,
            [
                'commandName' => $commandName,
                'args' => $args,
                'opts' => $options
            ],
            MsgLevel::INFO
        );
    }

}