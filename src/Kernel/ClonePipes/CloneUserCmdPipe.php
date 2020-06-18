<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\ClonePipes;

use Commune\Ghost\Cmd\AGhostCmdPipe;

/**
 * 用户命令管道.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneUserCmdPipe extends AGhostCmdPipe
{
    public function getCommandMark(): string
    {
        return '#';
    }

    public function getAuthPolicies(): array
    {
        return [];
    }

    public function getCommands(): array
    {
        return $this->cloner->config->userCommands;
    }


}