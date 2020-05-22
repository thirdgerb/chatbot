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
 * @property-read string $command
 * @property-read string $error
 */
class CommandErrorInt extends IIntentMsg
{
    const DEFAULT_LEVEL = HostMsg::ERROR;
    const INTENT_NAME = HostMsg\IntentMsg::SYSTEM_COMMAND_ERROR;

    public function __construct(
        string $commandName = '',
        string $errorMsg = ''
    )
    {
        parent::__construct(
            '',
            [
                'command' => $commandName,
                'error' => $errorMsg,
            ]
        );
    }

    public static function create(array $data = []): Struct
    {
        return new static(
            $data['command'] ?? '',
            $data['error'] ?? ''
        );
    }

    public static function intentStub(): array
    {
        return [
            'command' => '',
            'error' => '',
        ];
    }

    public function getText(): string
    {
        return $this->error;
    }
}