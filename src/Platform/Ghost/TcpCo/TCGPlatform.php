<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Ghost\TcpCo;

use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Host;
use Swoole;
use Swoole\Coroutine;                                      //      //
use Commune\Blueprint\Ghost;                            // /     // /
use Commune\Blueprint\Kernel\Protocals\GhostResponse;               //
use Commune\Blueprint\Kernel\Protocals\AppRequest;                  //
use Commune\Contracts\Messenger\GhostMessenger;                    //
use Commune\Kernel\Protocals\IGhostRequest;       //*/   /*/       //
use Commune\Support\Utils\TypeUtils;                              //
use Psr\Log\LoggerInterface;                                     //
use Commune\Platform\AbsPlatform;       ////////               //
use Commune\Blueprint\Platform;                             //
use Commune\Blueprint\Kernel\Handlers\GhostRequestHandler;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;


/**
 * TCP - 协程 - Ghost 平台
 * 通过监听 TCP 端口, 对外提供 Ghost 的服务.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TCGPlatform extends AbsPlatform
{
    /**
     * @var TCGServerOption
     */
    protected $option;

    /**
     * @var Ghost
     */
    protected $ghost;


    /*----- cached -----*/

    /**
     * @var GhostMessenger
     */
    protected $messenger;

    /**
     * @var Swoole\Process\Pool
     */
    protected $pool;



    public function __construct(
        Host $host,
        PlatformConfig $config,
        LoggerInterface $logger,
        TCGServerOption $option,
        Ghost $ghost
    )
    {
        $this->option = $option;
        $this->ghost = $ghost;
        parent::__construct($host, $config, $logger);
    }

    /**
     * @param float $seconds
     */
    public function sleep(float $seconds): void
    {
        Coroutine::sleep($seconds);
    }

    /**
     * 关闭.
     */
    public function shutdown(): void
    {
        if (isset($this->pool)) {
            $this->pool->shutdown();
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


    public function serve(): void
    {
        // 准备进程池.
        $pool = $this->initPool();
        $this->pool = $pool;

        $pool->on('workerStart', function ($pool, $id) {

            $server = $this->initServer();
            $chan = $this->initAsyncChan();

            //收到15信号关闭服务
            Swoole\Process::signal(SIGTERM, function () use ($server) {
                $server->shutdown();
            });

            //接收到新的连接请求 并自动创建一个协程
            $server->handle([$this, 'receive']);

            // 处理异步逻辑
            $chanNum = $this->option->chanNum;
            for ($i = 0; $i <= $chanNum; $i++) {
                Coroutine::create(function() use ($chan){
                    while (true) {
                        $data = $chan->pop();
                        $this->async($data);
                    }
                });
            }

            //开始监听端口
            $server->start();
        });
        $pool->start();
    }


    /**
     * 响应一个双工的 Connection
     *
     * @param Coroutine\Server\Connection $conn
     */
    public function receive(Coroutine\Server\Connection $conn) : void
    {
        while (true) {
            //接收数据
            $data = $conn->recv();
            if (empty($data)) {
                $conn->close();
                break;
            }

            //发送数据
            $conn->send('hello');

            \Co::sleep(1);
        }
    }

    /**
     * 异步响应.
     * @param $data
     */
    public function async($data) : void
    {
        if (!$data instanceof GhostRequest) {
            $type = TypeUtils::getType($data);
            $this->logger->error(
                __METHOD__
                . "receive invalid channel package, $type given"
            );
            return;
        }

        if (!$data->isAsync()) {
            $this->logger->error(
                __METHOD__
                . ' only accept async request',
                IGhostRequest::toLogContext($data)
            );
        }

        try {
            /**
             * @var GhostResponse $response
             */
            $response = $this->ghost->handleRequest($data, GhostRequestHandler::class);

            $this->logger->info(
                'handled async ghost request',
                IGhostRequest::toLogContext($data)
            );

            unset($request, $response);

        } catch (\Throwable $e) {
            $this->catchExp($e);
        }

    }


    protected function initAsyncChan() : Coroutine\Channel
    {
        $chan = new Coroutine\Channel($this->option->chanCapacity);
        $this->messenger = new TCGhostMessenger(
            $this->option,
            $this->logger,
            $chan
        );

        // 绑定 GhostMessenger 到容器.
        $this->host->instance(GhostMessenger::class, $this->messenger);

        return $chan;
    }

    /**
     * @return Coroutine\Server
     * @throws Swoole\Exception
     */
    protected function initServer() : Swoole\Coroutine\Server
    {
        // 设置 server 启动参数.
        $server = new Swoole\Coroutine\Server(
            $this->option->host,
            $this->option->port,
            $this->option->ssl,
            true
        );

        //收到15信号关闭服务
        Swoole\Process::signal(SIGTERM, function () use ($server) {
            $server->shutdown();
        });

        return $server;
    }


    /**
     * 初始化进程池.
     * @return Swoole\Process\Pool
     */
    protected function initPool() : Swoole\Process\Pool
    {
        $pool = new Swoole\Process\Pool($this->option->workerNum);

        // 设置 Server 的配置.
        $serverOption = $this->option->serverOption;
        // 必须允许协程.
        $serverOption['enable_coroutine'] = true;
        $pool->set($serverOption);
        return $pool;
    }


}