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
use Commune\Support\Struct\Struct;


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
    const DEFAULT_LEVEL = HostMsg::INFO;
    const INTENT_NAME = HostMsg\DefaultIntents::SYSTEM_COMMAND_DESC;

    public function __construct(
        string $command,
        string $desc,
        string $arguments,
        string $options
    )
    {
        parent::__construct(
            '',
            get_defined_vars()
        );
    }

    public static function intentStub(): array
    {
        return [
            'command' => '',
            'desc' => '',
            'arguments' => '',
            'options' => '',
        ];
    }

    public static function create(array $data = []): Struct
    {
        return new static(
            $data['command'] ?? '',
            $data['desc'] ?? '',
            $data['arguments'] ?? '',
            $data['options'] ?? ''
        );
    }

    public function getText(): string
    {
        $command = $this->command;
        $desc = $this->desc;
        $args = $this->arguments;
        $opts = $this->options;
        return "$command: $desc\n$args\n$opts";
    }
}