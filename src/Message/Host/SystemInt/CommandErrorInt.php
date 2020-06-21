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
 * @property-read string $error
 */
class CommandErrorInt extends IIntentMsg
{
    const DEFAULT_LEVEL = HostMsg::ERROR;
    const INTENT_NAME = HostMsg\DefaultIntents::SYSTEM_COMMAND_ERROR;

    public static function instance(
        string $command = '',
        string $error = ''
    ) : self
    {
        return new static(get_defined_vars());
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