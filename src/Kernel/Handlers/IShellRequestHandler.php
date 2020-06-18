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
use Commune\Framework\Spy\SpyAgency;
use Commune\Kernel\Protocals\IGhostRequest;
use Commune\Kernel\Protocals\IShellOutputRequest;
use Commune\Kernel\Protocals\IShellOutputResponse;
use Commune\Protocals\HostMsg;
use Commune\Kernel\ShellPipes;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellRequestHandler implements ShellRequestHandler
{
    protected $middleware = [
        // guard
        ShellPipes\ShellGuardPipe::class,
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
     * 直接给出最终的结果.
     * @param ShellInputRequest $request
     * @param HostMsg[] $outputs
     * @return ShellOutputResponse
     */
    protected function inputToOutputResponse(
        ShellInputRequest $request,
        HostMsg ...$outputs
    ) : ShellOutputResponse
    {
        $input = $request->getInput();

        if (!empty($outputs)) {
            $outputs = array_map(function(HostMsg $message) use ($input) {
                return $input->output($message);
            }, $outputs);
        }

        return new IShellOutputResponse([
            'sessionId' => $request->getSessionId(),
            'traceId' => $request->getTraceId(),
            'errcode' => 0,
            'errmsg' => '',
            'outputs' => $outputs
        ]);

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
         * @var Messenger $messenger
         */
        $messenger = $this->session->container->get(Messenger::class);
        $messenger->asyncSendGhostInputs($input);

        return $this->emptyResponse($response);
    }

    /**
     * @param ShellInputResponse $response
     * @return GhostResponse
     */
    protected function send2Ghost(ShellInputResponse $response) : GhostResponse
    {
        $request = $this->wrapInputRequest($response);

        // 如果已经实例化了 Ghost, 就做同步响应.
        $container = $this->session->container;
        if ($container->bound(Ghost::class)) {
            /**
             * @var Ghost $ghost
             */
            $ghost = $container->make(Ghost::class);
            return $ghost->handleRequest($request);
        }

        /**
         * @var Messenger $messenger
         */
        $messenger = $container->get(Messenger::class);
        $response = $messenger->sendInput2Ghost($request);
        return $response;
    }

    /**
     * 空响应
     * @param AppResponse $response
     * @return ShellOutputResponse
     */
    protected function emptyResponse(AppResponse $response) : ShellOutputResponse
    {
        return new IShellOutputResponse([
            'sessionId' => $response->getSessionId(),
            'traceId' => $response->getTraceId(),
            'errcode' => $response->getErrcode(),
            'errmsg' => $response->getErrmsg(),
            'outputs' => []
        ]);
    }

    /**
     * @param ShellInputResponse $response
     * @return GhostRequest
     */
    protected function wrapInputRequest(ShellInputResponse $response) : GhostRequest
    {
        $sessionId = '';
        if ($this->session->isSingletonInstanced('storage')) {
            $sessionId = $this->session->storage->cloneSessionId
                ?? $sessionId;
        }

        return new IGhostRequest([
            'sessionId' => $sessionId,
            'async' => false,
            'input' => $response->getInput(),
        ]);
    }

    /**
     * @param GhostResponse $response
     * @return ShellOutputRequest
     */
    protected function wrapOutputRequest(GhostResponse $response) : ShellOutputRequest
    {
        return new IShellOutputRequest([
            'sessionId' => $response->getSessionId(),
            'traceId' => $response->getTraceId(),
            'async' => false,
            'stateless' => false,
            'outputs' => [],
        ]);
    }

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }
}