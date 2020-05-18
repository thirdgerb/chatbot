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
use Psr\Log\LoggerInterface;
use Commune\Container\ContainerContract;
use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Framework\Command\TRequestCmdPipe;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;

/**
 * 用户命令管道.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneUserCmdPipe extends AClonePipe implements RequestCmdPipe
{
    use TRequestCmdPipe;

    public function getCommandMark(): string
    {
        return '#';
    }

    public function getCommands(): array
    {
        return $this->cloner->config->userCommands;
    }

    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        $response = $this->tryHandleCommand($request, $next);
        return $response instanceof GhostResponse
            ? $response
            : $request->fail(AppResponse::HOST_LOGIC_ERROR);
    }


    public function getContainer(): ContainerContract
    {
        return $this->cloner->container;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->cloner->logger;
    }

    public function getInputText(AppRequest $request): ? string
    {
        if (!$request instanceof GhostRequest) {
            return null;
        }

        $message = $request->getInput()->getMessage();
        if ($message instanceof VerbalMsg) {
            return $message->getNormalizedText();
        }

        return null;
    }


}