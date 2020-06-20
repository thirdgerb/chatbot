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
use Commune\Protocals\HostMsg\DefaultIntents;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CancelCmd extends AGhostCmd
{
    const SIGNATURE = 'cancel';

    const DESCRIPTION = '退出当前上下文语境';

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $this->cloner
            ->input
            ->comprehension
            ->intention
            ->setMatchedIntent(DefaultIntents::GUEST_NAVIGATE_CANCEL);

        $this->goNext();
    }

}