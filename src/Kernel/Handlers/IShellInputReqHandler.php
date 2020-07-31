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

use Commune\Blueprint\Framework\Pipes\RequestPipe;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Kernel\Handlers\GhostRequestHandler;
use Commune\Blueprint\Kernel\Handlers\ShellOutputReqHandler;
use Commune\Blueprint\Kernel\Handlers\ShellInputReqHandler;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellInputResponse;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Contracts\Messenger\ShellMessenger;
use Commune\Framework\Spy\SpyAgency;
use Commune\Kernel\Protocals\IGhostRequest;
use Commune\Kernel\Protocals\IShellOutputRequest;
use Commune\Kernel\Protocals\IShellOutputResponse;
use Commune\Kernel\ShellPipes;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellInputReqHandler implements ShellInputReqHandler
{
    protected $middleware = [
        // guard
        ShellPipes\ShellTryCatchPipe::class,
        // api 管道
        ShellPipes\InputApiPipe::class,
        // command
        ShellPipes\InputCommandPipe::class,
        // input parser
        ShellPipes\InputParserPipe::class,
    ];


    /**
     * @var ShellSession
     */
    protected $session;

    /**
     * IShellRequestHandler constructor.
     * @param ShellSession $session
     * @param array|null $middleware
     */
    public function __construct(ShellSession $session, array $middleware = null)
    {
        $this->session = $session;
        $this->middleware = $middleware ?? $this->middleware;

        SpyAgency::incr(static::class);
    }


    public function __invoke(ShellInputRequest $request): ShellOutputResponse
    {
        $invalid = $request->isInvalid();
        if (isset($invalid)) {
            $badResponse = $request->response(
                AppResponse::BAD_REQUEST,
                $invalid
            );
            return $this->emptyResponse($badResponse);
        }

        // 启动流程
        $inputRes = $this->handleInputRequest($request);

        // 如果请求失败了, 直接返回结果.
        if ($inputRes->hasOutputs()) {
            return $this->inputToOutputResponse($inputRes);
        }
        if (!$inputRes->isForward()) {
            return $this->emptyResponse($inputRes);

        // 如果是异步请求, 则异步发送响应, 并不等待结果.
        } elseif ($inputRes->isAsync()) {
            return $this->asyncSend2Ghost($inputRes);
        }

        // 发送同步响应.
        $ghostRes = $this->send2Ghost($inputRes);

        // 如果 ghost 端的响应异常.
        if (!$ghostRes->isForward()) {
            return $this->emptyResponse($ghostRes);
        }

        // 处理响应逻辑.
        $outputRequest = $this->wrapOutputRequest($request, $ghostRes);

        /**
         * @var ShellOutputReqHandler $handler
         */
        $handler = $this->session->shell->firstProtocalHandler(
            $this->session->container,
            $outputRequest,
            ShellOutputReqHandler::class
        );

        return $handler($outputRequest);
    }



    protected function handleInputRequest(ShellInputRequest $request) : ShellInputResponse
    {
        $middleware = $this->middleware;

        if (empty($middleware)) {
            return $this->finale($request);
        }

        // 生成管道.
        $pipeline = $this->session->buildPipeline(
            $middleware,
            RequestPipe::HANDLER_FUNC,
            function(ShellInputRequest $request) : ShellInputResponse{
                return $this->finale($request);
            }
        );

        // 通过管道运行.
        return $pipeline($request);
    }

    protected function finale(ShellInputRequest $request) : ShellInputResponse
    {
        return $request->response();
    }

    /**
     * 将消息异步地推送给 Ghost, 也会拿到异步的响应.
     *
     * @param ShellInputResponse $response
     * @return ShellOutputResponse
     */
    protected function asyncSend2Ghost(ShellInputResponse $response) : ShellOutputResponse
    {
        $input = $response->getInput();

        /**
         * @var ShellMessenger $messenger
         */
        $messenger = $this->session->container->get(ShellMessenger::class);

        $request = IGhostRequest::instance(
            $this->session->getAppId(),
            false,
            $input,
            $response->getEntry(),
            $response->getEnv(),
            $response->getComprehension(),
            false,
            $response->getTraceId()
        );

        $messenger->sendGhostRequest($request);

        return $this->emptyResponse($response);
    }

    /**
     * @param ShellInputResponse $response
     * @return GhostResponse
     */
    protected function send2Ghost(ShellInputResponse $response) : GhostResponse
    {
        $request = $this->wrapGhostRequest($response);

        // 如果已经实例化了 Ghost, 就做同步响应.
        $container = $this->session->container;
        /**
         * @var ShellMessenger $messenger
         */
        $messenger = $container->get(ShellMessenger::class);
        $response = $messenger->sendGhostRequest($request);
        return $response;
    }

    /**
     * 空响应
     * @param AppResponse $response
     * @return ShellOutputResponse
     */
    protected function emptyResponse(AppResponse $response) : ShellOutputResponse
    {
        return IShellOutputResponse::instance(
            $response->getErrcode(),
            $response->getErrmsg(),
            [],
            $response->getSessionId(),
            $response->getBatchId(),
            $response->getTraceId()
        );
    }

    /**
     * input 直接变成 output
     *
     * @param ShellInputResponse $response
     * @return ShellOutputResponse
     */
    protected function inputToOutputResponse(ShellInputResponse $response) : ShellOutputResponse
    {
        return IShellOutputResponse::instance(
            $response->getErrcode(),
            $response->getErrmsg(),
            $response->getOutputs(),
            $response->getSessionId(),
            $response->getBatchId(),
            $response->getTraceId()
        );
    }


    /**
     * @param ShellInputResponse $response
     * @return GhostRequest
     */
    protected function wrapGhostRequest(ShellInputResponse $response) : GhostRequest
    {
        $request = IGhostRequest::instance(
            $this->session->getAppId(),
            $response->isAsync(),
            $response->getInput(),
            $response->getEntry(),
            $response->getEnv(),
            $response->getComprehension(),
            false,
            $response->getTraceId()
        );

        // 检查路由的目标 ID 是否存在.
        $sessionId = $this->session->storage->cloneSessionId;

        // 设定前往 Clone 的目标 session id.
        if (isset($sessionId)) {
            $request->routeToSession($sessionId);
        }

        return $request;
    }

    /**
     * @param ShellInputRequest $request
     * @param GhostResponse $response
     * @return ShellOutputRequest
     */
    protected function wrapOutputRequest(
        ShellInputRequest $request,
        GhostResponse $response
    ) : ShellOutputRequest
    {
        $input = $request->getInput();

        return IShellOutputRequest::instance(
            false,
            $response->getSessionId(),
            $response->getTraceId(),
            $response->getBatchId(),
            $response->getOutputs(),
            $input->getCreatorId(),
            $input->getCreatorName()
        );
    }

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }
}