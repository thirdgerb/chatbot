<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype;

use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Blueprint\Server\Request;
use Commune\Framework\Blueprint\Server\Response;
use Commune\Framework\Blueprint\Session;
use Commune\Framework\Prototype\Kernel\AAppKernel;
use Commune\Ghost\Blueprint\Ghost;
use Commune\Ghost\Blueprint\GhostKernel;
use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Contracts\GhostRequest;
use Commune\Ghost\Contracts\GhostResponse;
use Commune\Ghost\GhostConfig;
use Commune\Ghost\Prototype\Kernel\AsyncGhostRequest;
use Commune\Ghost\Prototype\Kernel\AsyncGhostResponse;
use Commune\Ghost\Prototype\Pipeline\AsyncChatLockerPipe;
use Commune\Ghost\Prototype\Pipeline\ChatLockerPipe;
use Commune\Ghost\Prototype\Pipeline\GhostMessengerPipe;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IGhostKernel extends AAppKernel implements GhostKernel
{
    /*---- config ----*/

    protected $headPipes = [
        ChatLockerPipe::class,
        GhostMessengerPipe::class,
    ];

    protected $rearPipes = [

    ];


    protected $asyncHeadPipes = [
        AsyncChatLockerPipe::class,
        GhostMessengerPipe::class
    ];

    protected $asyncRearPipes = [

    ];


    /*---- cached ----*/

    /**
     * @var Ghost
     */
    protected $app;

    /**
     * @var GhostConfig
     */
    protected $config;

    public function __construct(Ghost $app)
    {
        $this->config = $app->getProcContainer()->make(GhostConfig::class);
        parent::__construct($app);
    }

    protected function basicReqBinding(ReqContainer $container): void
    {
        $container->alias(GhostRequest::class, Request::class);
        $container->alias(GhostResponse::class, Response::class);
    }

    protected function makeSession(ReqContainer $container): Session
    {
        $session = $container->make(Conversation::class);
        return $session;
    }


    public function onSync(
        GhostRequest $request,
        GhostResponse $response
    ): void
    {
        $middleware = [];

        $this->handleRequest(
            $request,
            $response,
            $middleware
        );
    }

    public function onAsync() : bool
    {
        $messenger = $this->app->getMessenger();
        $input = $messenger->popInput();
        if (empty($input)) {
            return false;
        }

        $request = new AsyncGhostRequest($input);
        $response = new AsyncGhostResponse($input);
        $middleware = [];

        $this->handleRequest(
            $request,
            $response,
            $middleware
        );
        return true;
    }


}