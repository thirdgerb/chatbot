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
 * @property-read string $cmdList
 */
class CommandListInt extends IIntentMsg
{
    public function __construct(string $cmdList)
    {
        parent::__construct(
            HostMsg\IntentMsg::SYSTEM_COMMAND_LIST,
            [
              'cmdList' => $cmdList
            ],
            HostMsg::INFO
        );
    }

    public function getNormalizedText(): string
    {
        return $this->cmdList;
    }
}