<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Commands\Super;

use Commune\Blueprint\Framework\Command\CommandMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Ghost\Cmd\AGhostCmd;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ProcessCmd extends AGhostCmd
{
    const SIGNATURE = 'process';

    const DESCRIPTION = '查看多轮对话进程的数据';


    protected function handle(CommandMsg $command, RequestCmdPipe $pipe): void
    {
        $process = $this->cloner->runtime->getCurrentProcess();
        $this->info($process->toPrettyJson());
    }
}