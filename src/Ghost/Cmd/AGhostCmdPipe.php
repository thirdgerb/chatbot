<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Cmd;

use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Container\ContainerContract;
use Commune\Framework\Command\TRequestCmdPipe;
use Commune\Ghost\ClonePipes\AClonePipe;
use Commune\Blueprint\Framework\Pipes\RequestCmdPipe;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Psr\Log\LoggerInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AGhostCmdPipe extends AClonePipe implements RequestCmdPipe
{
    use TRequestCmdPipe;

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
            // 区分大小写
            return $message->getText();
        }

        return null;
    }


}