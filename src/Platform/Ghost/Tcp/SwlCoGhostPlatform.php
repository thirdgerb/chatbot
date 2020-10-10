<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Ghost\Tcp;

use Commune\Kernel\Protocols\LogContext;
use Swoole;
use Swoole\Coroutine;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Host;
use Psr\Log\LoggerInterface;
use Commune\Platform\AbsPlatform;
use Commune\Blueprint\Platform;
use Commune\Support\Swoole\SwooleUtils;
use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Contracts\Messenger\GhostMessenger;
use Commune\Blueprint\Kernel\Protocols\AppRequest;
use Commune\Blueprint\Kernel\Handlers\GhostRequestHandler;
use Commune\Platform\Libs\SwlCo\ProcPoolFactory;
use Commune\Platform\Libs\SwlCo\TcpPlatformOption;
use Commune\Platform\Libs\SwlCo\TcpPlatformServeTrait;
use Commune\Blueprint\Kernel\Protocols\GhostRequest;



/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwlCoGhostPlatform extends AbsPlatform
{
    use TcpPlatformServeTrait;

    /**
     * @var Ghost
     */
    protected $ghost;

    /**
     * @var SwlCoGhostOption
     */
    protected $option;

    /**
     * @var ProcPoolFactory
     */
    protected $poolFactory;

    public function __construct(
        Host $host,
        PlatformConfig $config,
        SwlCoGhostOption $option,
        Ghost $ghost,
        LoggerInterface $logger
    )
    {
        $this->ghost = $ghost;
        $this->option = $option;

        $this->poolFactory = new ProcPoolFactory($option->serverOption);

        parent::__construct($host, $config, $logger);
    }

    public function getAppId(): string
    {
        return $this->ghost->getId();
    }


    public function sleep(float $seconds): void
    {
        if (SwooleUtils::isInCoroutine()) {
            Coroutine::sleep($seconds);
        }
    }

    public function shutdown(): void
    {
        $this->host->getConsoleLogger()->info(static::class . '::'. __FUNCTION__);
        $this->poolFactory->shutdown();
    }


    public function serve(): void
    {
        Swoole\Runtime::enableCoroutine();
        $pool = $this->poolFactory->getPool();

        $pool->on('workerStart', function ($pool, $id) {

            $server = $this->poolFactory->createServer();

            //接收到新的连接请求 并自动创建一个协程
            $server->handle([$this, 'receive']);

            // 使用一个协程来处理异步的消息.
            Coroutine::create([$this, 'receiveAsync']);

            $host = $this->poolFactory->getOption()->host;
            $port = $this->poolFactory->getOption()->port;

            $this->host->getConsoleLogger()->info("server start on host $host, port $port");
            //开始监听端口
            $server->start();
        });

        $pool->start();
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
            Coroutine::create(
                function(GhostRequest $request, Ghost $ghost, LoggerInterface $logger){

                    // 处理异步响应. 理论上都被广播了.
                    $response = $ghost->handleRequest(
                        $request,
                        GhostRequestHandler::class
                    );

                    // 记录日志.
                    $logger->info(
                        'handled async ghost request',
                        LogContext::requestToContext($request)
                    );

                    unset($request, $response);

                },
                $request,
                $this->ghost,
                $this->logger
            );

            unset($request);
        }
    }

    protected function handleRequest(Platform\Adapter $adapter, AppRequest $request, string $interface = null): void
    {
        $interface = $interface ?? GhostRequestHandler::class;
        $response = $this
            ->ghost
            ->handleRequest($request, $interface);

        $adapter->sendResponse($response);

        unset($request, $response, $adapter);
    }

    public function getAdapterOption(): TcpPlatformOption
    {
        return $this->option->adapterOption;
    }


}