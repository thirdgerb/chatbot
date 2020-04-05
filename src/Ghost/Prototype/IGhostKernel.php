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
use Commune\Framework\Prototype\Kernel\AAppKernel;
use Commune\Ghost\Blueprint\Ghost;
use Commune\Ghost\Blueprint\GhostKernel;
use Commune\Ghost\Contracts\GhtRequest;
use Commune\Ghost\Contracts\GhtResponse;
use Commune\Ghost\GhostConfig;
use Commune\Ghost\Prototype\Kernel\AsyncGhtRequest;
use Commune\Ghost\Prototype\Kernel\AsyncGhtResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IGhostKernel extends AAppKernel implements GhostKernel
{
    /*---- config ----*/

    protected $syncMiddleware = [];

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

    public function basicReqBinding(ReqContainer $container): void
    {
        $container->singleton(GhtRequest::class, Request::class);
        $container->singleton(GhtResponse::class, Response::class);
    }


    public function onSync(
        GhtRequest $request,
        GhtResponse $response
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

        $request = new AsyncGhtRequest($input);
        $response = new AsyncGhtResponse($input);
        $middleware = [];

        $this->handleRequest(
            $request,
            $response,
            $middleware
        );
        return true;
    }


}