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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class QuitCmd extends AGhostCmd
{
    const SIGNATURE = 'quit';

    const DESCRIPTION = '退出会话';

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $this->cloner->endConversation();
    }


}