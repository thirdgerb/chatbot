<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\Handlers;

use Commune\Blueprint\Kernel\Handlers\ShellOutputHandler;
use Commune\Blueprint\Kernel\Handlers\ShellRequestHandler;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellInputResponse;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Contracts\Messenger\Messenger;
use Commune\Protocals\Intercom\InputMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellRequestHandler implements ShellRequestHandler
{
    protected $inputMiddleware = [

    ];


    /**
     * @var ShellSession
     */
    protected $session;

    public function __invoke(ShellInputRequest $request): ShellOutputResponse
    {
        $inputRes = $request->validate() ?? $this->handleInputRequest($request);

        // 如果请求失败了, 直接返回结果.
        if (!$inputRes->isSuccess()) {
            return $this->emptyResponse($inputRes);
        }

        // 如果是异步请求, 则异步发送响应, 并不等待结果.
        if ($inputRes->isAsync()) {
            return $this->asyncSend2Ghost($inputRes);
        }

        // 如果 ghost 端的响应异常.
        $ghostRes = $this->send2Ghost($inputRes);
        if (!$ghostRes->isSuccess()) {
            return $this->emptyResponse($ghostRes);
        }

        $outputRequest = $this->wrapOutputRequest($ghostRes);

        /**
         * @var ShellOutputHandler $handler
         */
        $handler = $this->session->shell->firstProtocalHandler(
            $this->session->container,
            $outputRequest,
            ShellOutputHandler::class
        );

        return $handler($outputRequest);
    }

    protected function handleInputRequest(ShellInputRequest $request) : ShellInputResponse
    {
    }


    protected function asyncSend2Ghost(ShellInputResponse $response) : ShellOutputResponse
    {
        $input = $response->getInput();
        /**
         * @var Messenger $messenger
         */
        $messenger = $this->session->container->get(Messenger::class);
        $messenger->asyncSendGhostInputs($input);

        return $this->emptyResponse($response);
    }

    protected function send2Ghost(ShellInputResponse $response) : GhostResponse
    {
        $request = $this->wrapInputRequest($response);
        /**
         * @var Messenger $messenger
         */
        $messenger = $this->session->container->get(Messenger::class);
        $response = $messenger->sendInput2Ghost($request);
        return $response;
    }

    protected function emptyResponse(AppResponse $response) : ShellOutputResponse
    {
    }

    protected function wrapInputRequest(ShellInputResponse $response) : GhostRequest
    {

    }

    protected function wrapOutputRequest(GhostResponse $response) : ShellOutputRequest
    {

    }
}