<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\ShellPipes;

use Commune\Contracts\Api\ApiController;
use Commune\Protocals\HostMsg\Convo\ApiMsg;
use Commune\Support\Utils\TypeUtils;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellInputResponse;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class InputApiPipe extends AShellPipe
{
    protected function handleInput(
        ShellInputRequest $request,
        \Closure $next
    ): ShellInputResponse
    {
        $message = $request->getInput();
        if (!$message instanceof ApiMsg) {
            return $next($request);
        }

        $response = $this->runApi($request);

        // shell 处理不了的 api 消息给 ghost
        if (!isset($response)) {
            return $next($request);
        }

        if (!$response instanceof ShellOutputResponse) {
            $type = TypeUtils::getType($response);
            $this->session->logger->error(
                __METHOD__
                . ' invalid response from api handler, api '
                . $message->getApiName()
                . ", $type given"
            );
        }

        return $response;
    }

    protected function runApi(ShellInputRequest $request)
    {
        $input = $request->getInput();
        $message = $input->getMessage();
        if (!$message instanceof ApiMsg) {
            return null;
        }

        $controller = $this->session->shell->firstProtocalHandler(
            $this->session->container,
            $message,
            ApiController::class
        );

        if (empty($controller)) {
            return null;
        }

        /**
         * @var ApiController $controller
         */
        return $controller($request, $message);
    }

    protected function handleOutput(
        ShellOutputRequest $request,
        \Closure $next
    ): ShellOutputResponse
    {
        return $next($request);
    }



}