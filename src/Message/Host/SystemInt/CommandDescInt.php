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
 *
 * @property-read string $command
 * @property-read string $desc
 * @property-read string $arguments
 * @property-read string $options
 */
class CommandDescInt extends IIntentMsg
{
    public function __construct(
        string $command,
        string $desc,
        string $arguments,
        string $options
    )
    {
        parent::__construct(
            HostMsg\IntentMsg::SYSTEM_COMMAND_DESC,
            [
                'command' => $command,
                'desc' => $desc,
                'arguments' => $arguments,
                'options' => $options
            ],
            HostMsg::INFO
        );
    }

    public function getNormalizedText(): string
    {
        $command = $this->command;
        $desc = $this->desc;
        $args = $this->arguments;
        $opts = $this->options;
        return "$command: $desc\n$args\n$opts";
    }
}