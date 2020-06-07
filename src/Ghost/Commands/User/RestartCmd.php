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
class RestartCmd extends AGhostCmd
{
    const SIGNATURE = 'restart';

    const DESCRIPTION = '当前对话语境从头开始';

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $this->cloner
            ->input
            ->comprehension
            ->intention
            ->setMatchedIntent(IntentMsg::GUEST_NAVIGATE_RESTART);

        $this->goNext();
    }

}