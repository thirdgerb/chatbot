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

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Exceptions\CommuneRuntimeException;
use Commune\Blueprint\Exceptions\Runtime\BrokenRequestException;
use Commune\Blueprint\Framework\Pipes\RequestPipe;
use Commune\Blueprint\Kernel\Handlers\GhostRequestHandler;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Framework\Spy\SpyAgency;
use Commune\Ghost\IOperate\OStart;
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
        ClonePipes\CloneGuardPipe::class,

        /*  可能直接中断的情况 */

        // api 响应
        ClonePipes\CloneApiHandlePipe::class,

        // locker
        ClonePipes\CloneLockerPipe::class,

        // 消息广播
        ClonePipes\CloneDeliveryPipe::class,

        /* 多轮对话逻辑相关 */

        // command
        ClonePipes\CloneUserCmdPipe::class,
        // super
        ClonePipes\CloneSuperCmdPipe::class,
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

        $middleware = $this->middleware;

        if (empty($middleware)) {
            return $this->runDialogManager($request);
        }

        $pipeline = $this->cloner->buildPipeline(
            $middleware,
            RequestPipe::HANDLER_FUNC,
            function(GhostRequest $request) : GhostResponse{
                return $this->runDialogManager($request);
            }
        );

        // 通过管道运行.
        return $pipeline($request);
    }

    /**
     * 运行多轮对话内核逻辑.
     *
     * @param GhostRequest $request
     * @return GhostResponse
     */
    protected function runDialogManager(GhostRequest $request) : GhostResponse
    {
        $operator = new OStart($this->cloner);
        $tracer = $this->cloner->runtime->trace;

        try {

            while (isset($operator)) {

                $tracer->record($operator);
                $operator = $operator->tick();
                if ($operator->isTicked()) {
                    break;
                }
            }

            unset($next);

        } catch (CommuneRuntimeException $e) {
            throw $e;

        } catch (\Throwable $e) {
            throw new BrokenRequestException($e->getMessage(), $e);

        } finally {
            // 调试模式下检查运行轨迹.
            if (CommuneEnv::isDebug()) {
                $tracer->log($this->cloner->logger);
            }
        }

        return $request->response();
    }



    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }
}