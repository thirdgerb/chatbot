<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\SwooleCo\TcpGhost;

use Swoole\Coroutine;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Host;
use Psr\Log\LoggerInterface;
use Commune\Platform\AbsPlatform;
use Commune\Blueprint\Platform;
use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Contracts\Messenger\GhostMessenger;
use Commune\Kernel\Protocals\IGhostRequest;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Handlers\GhostRequestHandler;
use Commune\Platform\SwooleCo\Supports\CoProcPoolFactory;
use Commune\Platform\SwooleCo\Supports\CoTcpAdapterOption;
use Commune\Platform\SwooleCo\Supports\CoTcpServeTrait;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SCTGhostPlatform extends AbsPlatform
{
    use CoTcpServeTrait;

    /**
     * @var Ghost
     */
    protected $ghost;

    /**
     * @var SCTGhostOption
     */
    protected $option;

    /**
     * @var CoProcPoolFactory
     */
    protected $poolFactory;

    public function __construct(
        Host $host,
        PlatformConfig $config,
        SCTGhostOption $option,
        Ghost $ghost,
        LoggerInterface $logger
    )
    {
        $this->option = $option;

        $this->poolFactory = new CoProcPoolFactory($option->poolOption);

        parent::__construct($host, $config, $logger);
    }


    public function sleep(float $seconds): void
    {
        Coroutine::sleep($seconds);
    }

    public function shutdown(): void
    {
        $this->poolFactory->shutdown();
    }


    public function serve(): void
    {
        $pool = $this->poolFactory->getPool();

        $pool->on('workerStart', function ($pool, $id) {

            $server = $this->poolFactory->createServer();

            //接收到新的连接请求 并自动创建一个协程
            $server->handle([$this, 'receive']);

            Coroutine::create([$this, 'receiveAsync']);

            //开始监听端口
            $server->start();
        });
    }

    public function receiveAsync() : void
    {
        /**
         * @var GhostMessenger $messenger
         */
        $messenger = $this->getContainer()->make(GhostMessenger::class);

        while (true) {

            // 异步的请求. 应该是协程.
            $request = $messenger->receiveAsyncRequest();

            if (empty($request)) {
                continue;
            }

            // 用一个协程来处理.
            Coroutine::create(function() use ($request){

                // 处理异步响应. 理论上都被广播了.
                $response = $this->ghost->handleRequest(
                    $request,
                    GhostRequestHandler::class
                );

                // 记录日志.
                $this->logger->info(
                    'handled async ghost request',
                    IGhostRequest::toLogContext($request)
                );

                unset($request, $response);

            });

            unset($request);
        }
    }

    protected function handleRequest(Platform\Adapter $adapter, AppRequest $request): void
    {
        $response = $this
            ->ghost
            ->handleRequest($request, GhostRequestHandler::class);

        $adapter->sendResponse($response);

        unset($request, $response, $adapter);
    }

    public function getAdapterOption(): CoTcpAdapterOption
    {
        return $this->option->adapterOption;
    }


}