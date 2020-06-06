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
use Commune\Support\Arr\ArrayAndJsonAble;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class WhoCmd extends AGhostCmd
{
    const SIGNATURE = 'who';

    const DESCRIPTION = '查看我是谁';

    protected function handle(CommandMsg $message, RequestCmdPipe $pipe): void
    {
        $this->info(json_encode([
            'id' => $this->cloner->input->getGuestId(),
            'name' => $this->cloner->input->getSenderName(),
        ], ArrayAndJsonAble::PRETTY_JSON));
    }


}