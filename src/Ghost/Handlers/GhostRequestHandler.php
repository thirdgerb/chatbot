<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Handlers;

use Commune\Framework\Spy\SpyAgency;
use Commune\Ghost\ClonePipes;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Blueprint\Framework\Pipes\RequestPipe;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Ghost\Handlers\GhtRequestHandler;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostRequestHandler implements GhtRequestHandler
{
    /**
     * @var string[]
     */
    protected $middleware = [
        // 检查消息类型
        ClonePipes\CloneMessengerPipe::class,
        // api 响应
        ClonePipes\CloneApiHandlePipe::class,
        // locker
        ClonePipes\CloneLockerPipe::class,
        // command
        ClonePipes\CloneUserCmdPipe::class,
        // super
        ClonePipes\CloneSuperCmdPipe::class,
        // dialog manager
        ClonePipes\CloneDialogManagerPipe::class,
    ];

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * GhostRequestHandler constructor.
     * @param Cloner $cloner
     * @param array|null $middleware
     */
    public function __construct(Cloner $cloner, array $middleware = null)
    {
        $this->cloner = $cloner;
        $this->middleware = $middleware ?? $this->middleware;
        SpyAgency::incr(static::class);
    }


    public function __invoke(GhostRequest $request) : GhostResponse
    {
        $start = microtime(true);

        // 不接受异常请求.
        if (!$request->isValid()) {
            return $request->response(AppResponse::BAD_REQUEST);
        }

        // 无状态标记
        if ($request->isStateless()) {
            $this->cloner->noState();
        }

        $end = function(GhostRequest $request) : GhostResponse {
            return $request->response(AppResponse::NO_CONTENT);
        };

        if (empty($this->middleware)) {
            $response = $end($request);

        } else {
            $pipeline = $this->cloner->buildPipeline(
                $this->middleware,
                RequestPipe::HANDLER_FUNC,
                $end
            );

            $response = $pipeline($request);
        }

        $end = microtime(true);
        $gap = round(($end - $start) * 1000000);
        $peak = memory_get_peak_usage();
        $this->cloner->logger->info("finish request in $gap, memory peak $peak");
        return $response;
    }

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }
}