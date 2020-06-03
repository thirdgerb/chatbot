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

use Commune\Ghost\ClonePipes;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Blueprint\Framework\Pipes\RequestPipe;
use Commune\Blueprint\Framework\Request\AppResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class GhostRequestHandler
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
    }


    public function __invoke(GhostRequest $request) : GhostResponse
    {
        if ($request->isStateless()) {
            $this->cloner->noState();
        }

        $end = function(GhostRequest $request) : GhostResponse {
            return $request->fail(AppResponse::NO_CONTENT);
        };

        if (empty($this->middleware)) {
            return $end($request);
        }

        $pipeline = $this->cloner->buildPipeline(
            $this->middleware,
            RequestPipe::HANDLER_FUNC,
            $end
        );

        $response = $pipeline($request);
        return $response;
    }

}