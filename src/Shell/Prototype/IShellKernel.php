<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype;

use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Blueprint\Server\Request;
use Commune\Framework\Blueprint\Server\Response;
use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Prototype\Kernel\AAppKernel;
use Commune\Shell\Blueprint\Session\ShellSession;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Blueprint\ShellKernel;
use Commune\Shell\Contracts\ShellRequest;
use Commune\Shell\Contracts\ShellResponse;
use Commune\Shell\Prototype\Kernel\AsyncShellRequest;
use Commune\Shell\Prototype\Kernel\AsyncShellResponse;
use Commune\Shell\Prototype\Pipeline\QuestionPipe;
use Commune\Shell\Prototype\Pipeline\RenderPipe;
use Commune\Shell\Prototype\Pipeline\ResponsePipe;
use Commune\Shell\Prototype\Pipeline\ShellMessengerPipe;
use Commune\Shell\ShellConfig;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IShellKernel extends AAppKernel implements ShellKernel
{

    protected $headPipes = [
        ResponsePipe::class,
        QuestionPipe::class,
    ];


    protected $rearPipes = [
        RenderPipe::class,
        ShellMessengerPipe::class,
    ];



    /*-------- cached --------*/

    /**
     * @var Shell
     */
    protected $app;

    /**
     * @var ShellConfig
     */
    protected $shellConfig;

    /**
     * IShellKernel constructor.
     * @param Shell $app
     */
    public function __construct(Shell $app)
    {
        $this->shellConfig = $app->getProcContainer()->make(ShellConfig::class);
        parent::__construct($app);
    }



    public function basicReqBinding(ReqContainer $container): void
    {
        $container->alias(ShellRequest::class, Request::class);
        $container->alias(ShellResponse::class, Response::class);
    }

    protected function makeSession(ReqContainer $container): Session
    {
        return $container->make(ShellSession::class);
    }


    public function onSync(
        ShellRequest $request,
        ShellResponse $response
    ): void
    {
        $middleware = array_merge(
            $this->headPipes,
            $this->shellConfig->pipeline,
            $this->rearPipes
        );
        $this->handleRequest($request, $response, $middleware);
    }

    public function onAsyncResponse(ShellResponse $response) : void
    {
        $request = new AsyncShellRequest($response);

        $middleware = array_merge(
            $this->headPipes,
            $this->shellConfig->pipeline,
            $this->rearPipes
        );

        // 异步回收逻辑.
        $this->asyncHandleResponse(
            $request,
            $response,
            $middleware
        );
    }

    public function onAsyncRequest(
        ShellRequest $request
    ): void
    {
        $response = new AsyncShellResponse($request);

        $middleware = array_merge(
            $this->headPipes,
            $this->shellConfig->pipeline,
            $this->rearPipes
        );

        $this->asyncHandleRequest(
            $request,
            $response,
            $middleware
        );
    }


}