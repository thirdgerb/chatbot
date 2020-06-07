<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Commands\User;

use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Ghost\Cmd\AGhostCmd;
use Commune\Protocals\HostMsg\IntentMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BackCmd extends AGhostCmd
{
    const SIGNATURE = 'back';

    const DESCRIPTION = '返回上一轮对话';

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $this->cloner
            ->input
            ->comprehension
            ->intention
            ->setMatchedIntent(IntentMsg::GUEST_NAVIGATE_BACK);

        $this->goNext();
    }

}