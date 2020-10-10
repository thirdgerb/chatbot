<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\GhostCmd\User;

use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Kernel\GhostCmd\AGhostCmd;
use Commune\Protocols\HostMsg\DefaultIntents;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RepeatCmd extends AbsIntentCmd
{
    const SIGNATURE = 'repeat';

    const DESCRIPTION = '重复当前对话';

    protected function getIntentName(): string
    {
        return DefaultIntents::GUEST_NAVIGATE_REPEAT;
    }


}