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
use Commune\Blueprint\Kernel\Handlers\ShellOutputReqHandler;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Shell\ShellSession;
use Commune\Framework\Spy\SpyAgency;
use Commune\Kernel\ShellPipes;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellOutputReqHandler implements ShellOutputReqHandler
{

    protected $middleware = [
        ShellPipes\ShellTryCatchPipe::class,
        ShellPipes\OutputAsyncFillMessagesPipe::class,
        ShellPipes\OutputRenderPipe::class,
    ];

    /**
     * @var ShellSession
     */
    protected $session;

    public function __construct(ShellSession $cloner, array $middleware = null)
    {
        $this->session = $cloner;
        $this->middleware = $middleware ?? $this->middleware;
        SpyAgency::incr(static::class);
    }



    public function __invoke(ShellOutputRequest $request): ShellOutputResponse
    {
        $invalid = $request->isInvalid();
        if (isset($invalid)) {
            return $request->response(
                AppResponse::BAD_REQUEST,
                $invalid
            );
        }

        $middleware = $this->middleware;

        if (empty($middleware)) {
            return $this->finale($request);
        }

        // 生成管道.
        $pipeline = $this->session->buildPipeline(
            $middleware,
            RequestPipe::HANDLER_FUNC,
            function(ShellOutputRequest $request) : ShellOutputResponse{
                return $this->finale($request);
            }
        );

        // 通过管道运行.
        return $pipeline($request);
    }

    protected function finale(ShellOutputRequest $request) : ShellOutputResponse
    {
        return $request->response();
    }


    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }
}