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
use Commune\Protocals\HostMsg\ApiMsg;
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
        // 异步请求不处理 api.
        if ($request->isAsync()) {
            return $next($request);
        }

        // 只处理 Api Msg
        $message = $request->getInput()->getMessage();
        if (!$message instanceof ApiMsg) {
            return $next($request);
        }

        // 获取响应.
        $controller = $this->runApi($request);

        // shell 处理不了的 api 消息给 ghost
        if (!isset($controller)) {
            return $next($request);
        }

        $expect = ApiController::class;
        $given = TypeUtils::getType($controller);
        $apiName = $message->getApiName();

        // 检查 handler 类型
        if (!$controller instanceof ApiController) {
            $this->session->logger->error(
                __METHOD__
                . " expect api controller extends $expect, $given given, api is $apiName"
            );

            return $next($request);
        }

        $response = $controller($request, $message);

        // 检查 response 类型.
        if (!$response instanceof ShellInputResponse) {
            $expect = ShellInputResponse::class;
            $actual = TypeUtils::getType($response);
            $this->session->logger->error(
                __METHOD__
                . " expect api controller response $expect, given $actual"
            );
        };

        return $next($request);
    }

    protected function runApi(ShellInputRequest $request) : ? ApiController
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

        return $controller;
    }

    protected function handleOutput(
        ShellOutputRequest $request,
        \Closure $next
    ): ShellOutputResponse
    {
        return $next($request);
    }



}