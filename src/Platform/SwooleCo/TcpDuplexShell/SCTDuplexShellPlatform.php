<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\SwooleCo\TcpSyncShell;

use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Host;
use Commune\Platform\SwooleCo\Supports\CoProcPoolOption;
use Commune\Platform\SwooleCo\Supports\CoTcpAdapterOption;
use Commune\Platform\SwooleCo\Supports\CoTcpServeTrait;
use Psr\Log\LoggerInterface;
use Swoole\Coroutine;
use Commune\Blueprint\Shell;
use Commune\Blueprint\Platform;
use Commune\Platform\AbsPlatform;
use Swoole\Coroutine\Server\Connection;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Platform\SwooleCo\Supports\CoTcpPacker;
use Commune\Platform\SwooleCo\Supports\CoProcPoolFactory;
use Commune\Blueprint\Kernel\Handlers\ShellInputReqHandler;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;


/**
 * Swoole Coroutine Sync Shell
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SCTDuplexShellPlatform extends AbsPlatform
{
    use CoTcpServeTrait;

    /**
     * @var SCTDuplexShellOption
     */
    protected $option;

    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var CoProcPoolFactory
     */
    protected $poolFactory;

    public function __construct(
        Host $host,
        PlatformConfig $config,
        SCTDuplexShellOption $option,
        CoProcPoolOption $poolOption,
        LoggerInterface $logger
    )
    {
        $this->option = $option;
        $this->poolFactory = new CoProcPoolFactory($option->poolOption);

        parent::__construct($host, $config, $logger);
    }


    public function shutdown(): void
    {
        $this->poolFactory->shutdown();
    }

    public function sleep(float $seconds): void
    {
        Coroutine::sleep($seconds);
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

    public function getAdapterOption(): CoTcpAdapterOption
    {
        return $this->option->adapterOption;
    }


    protected function handleRequest(Platform\Adapter $adapter, AppRequest $request): void
    {
        /**
         * @var ShellOutputResponse
         */
        $response = $this->shell->handleRequest(
            $request,
            ShellInputReqHandler::class
        );

        // 发送响应.
        $adapter->sendResponse($response);
    }
}