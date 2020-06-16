<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\ClonePipes;

use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Ghost\Cmd\AGhostCmdPipe;
use Psr\Log\LoggerInterface;
use Commune\Container\ContainerContract;
use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Kernel\Protocals\CloneRequest;
use Commune\Blueprint\Kernel\Protocals\CloneResponse;
use Commune\Framework\Command\TRequestCmdPipe;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;

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

    public function getCommands(): array
    {
        return $this->cloner->config->userCommands;
    }


}