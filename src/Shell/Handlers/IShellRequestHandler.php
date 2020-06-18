<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Handlers;

use Commune\Blueprint\Framework\Pipes\RequestPipe;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Shell\Handlers\ShellRequestHandler;
use Commune\Blueprint\Shell\Requests\ShellRequest;
use Commune\Blueprint\Shell\Requests\ShlInputRequest;
use Commune\Blueprint\Shell\Requests\ShlOutputRequest;
use Commune\Blueprint\Shell\Responses\ShellResponse;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Shell\ShellPipes;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellRequestHandler implements ShellRequestHandler
{

    /*-------- config --------*/

    protected $inputReqMiddleware = [
        ShellPipes\ShellApiHandlerPipe::class,
        ShellPipes\ShellInputParserPipe::class,
    ];

    protected $outputReqMiddleware = [
        ShellPipes\ShellOutputRenderPipe::class
    ];

    /*-------- cached --------*/

    /**
     * @var ShellSession
     */
    protected $session;

    /**
     * @var string[]|null
     */
    protected $middleware;

    /**
     * ShellRequestHandler constructor.
     * @param ShellSession $session
     * @param null|string[] $middleware
     */
    public function __construct(ShellSession $session, array $middleware = null)
    {
        $this->session = $session;
        $this->middleware = $middleware;
    }


    public function __invoke(ShellRequest $request) : ShellResponse
    {
        $start = microtime(true);

        // 不接受异常请求.
        if (!$request->isValid()) {
            return $request->fail(AppResponse::BAD_REQUEST);
        }

        // 无状态标记
        if ($request->isStateless()) {
            $this->session->noState();
        }

        $end = function(ShellRequest $request) : ShellResponse {
            return $request->fail(AppResponse::NO_CONTENT);
        };

        $middleware = $this->middlware
            ?? $this->getMiddleware($request);

        if (empty($middleware)) {
            $response = $end($request);

        } else {
            $pipeline = $this->session->buildPipeline(
                $middleware,
                RequestPipe::HANDLER_FUNC,
                $end
            );

            $response = $pipeline($request);
        }

        $end = microtime(true);
        $gap = round(($end - $start) * 1000000);
        $peak = memory_get_peak_usage();

        $reqType = get_class($request);
        $this->session->logger->info("finish shell request $reqType in $gap, memory peak $peak");

        return $response;
    }

    protected function getMiddleware(ShellRequest $request) : array
    {
        if ($request instanceof ShlInputRequest) {
            return $this->inputReqMiddleware;
        }

        if ($request instanceof ShlOutputRequest) {
            return $this->outputReqMiddleware;
        }

        return [];
    }
}