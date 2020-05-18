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
use Commune\Protocals\HostMsg\IntentMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $command
 * @property-read string $error
 */
class CommandErrorInt extends IIntentMsg
{

    public function __construct(
        string $commandName = '',
        string $errorMsg = ''
    )
    {
        parent::__construct(
            IntentMsg::SYSTEM_COMMAND_ERROR,
            [
                'command' => $commandName,
                'error' => $errorMsg,
            ],
            HostMsg::ERROR
        );
    }

    public function getNormalizedText(): string
    {
        return $this->error;
    }
}