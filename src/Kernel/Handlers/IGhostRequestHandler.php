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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Framework\Pipes\RequestPipe;
use Commune\Blueprint\Kernel\Handlers\GhostRequestHandler;
use Commune\Blueprint\Kernel\Protocols\AppResponse;
use Commune\Blueprint\Kernel\Protocols\GhostRequest;
use Commune\Blueprint\Kernel\Protocols\GhostResponse;
use Commune\Framework\Spy\SpyAgency;
use Commune\Kernel\ClonePipes;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IGhostRequestHandler implements GhostRequestHandler
{

    /**
     * @var Cloner
     */
    protected $cloner;

    /**
     * @var string[]
     */
    protected $middleware = [
        // 请求异常管理
        ClonePipes\CloneTryCatchPipe::class,

        /*  可能直接中断的情况 */

        // api 响应
        ClonePipes\CloneApiHandlePipe::class,

        // locker
        ClonePipes\CloneLockerPipe::class,

        // 路由.
        ClonePipes\CloneRoutePipe::class,

        // 消息广播
        ClonePipes\CloneBroadcastPipe::class,

        /* 多轮对话逻辑相关 */

        // command
        ClonePipes\CloneUserCmdPipe::class,
        // super
        ClonePipes\CloneSuperCmdPipe::class,

        // 对话管理
        ClonePipes\CloneDialogManagerPipe::class,
    ];

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

    public function __invoke(GhostRequest $request): GhostResponse
    {
        $invalid = $request->isInvalid();
        if (isset($invalid)) {
            return $request->response(
                AppResponse::BAD_REQUEST,
                $invalid
            );
        }

        if ($request->isStateless()) {
            $this->cloner->noState();
        }

        // 配置 convoId
        $convoId = $this->cloner->getConversationId();
        $this->cloner->input->setConvoId($convoId);

        // 执行管道.
        $middleware = $this->middleware;

        if (empty($middleware)) {
            return $request->response();
        }

        $pipeline = $this->cloner->buildPipeline(
            $middleware,
            RequestPipe::HANDLER_FUNC,
            function(GhostRequest $request) : GhostResponse{
                return $request->response();
            }
        );

        // 通过管道运行.
        return $pipeline($request);
    }

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }
}