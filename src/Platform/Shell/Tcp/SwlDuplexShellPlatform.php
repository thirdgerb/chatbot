<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Shell\Tcp;

use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Host;
use Psr\Log\LoggerInterface;
use Swoole;
use Swoole\Server;
use Swoole\Process;
use Commune\Blueprint\Kernel\Handlers\ShellInputReqHandler;
use Commune\Blueprint\Kernel\Handlers\ShellOutputReqHandler;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Contracts\Messenger\Broadcaster;
use Commune\Platform\Libs\SwlAsync\TcpPacker;
use Commune\Platform\Libs\SwlAsync\SwlAsyncPlatform;
use Commune\Support\Swoole\SwooleUtils;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Platform;
use Commune\Blueprint\Shell;
use Commune\Platform\AbsPlatform;


/**
 * 基于 Swoole Tcp Server 实现的双工 Shell 平台.
 * 用 Swoole Table 来保存路由关系, 收到广播后推送给客户端.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwlDuplexShellPlatform extends AbsPlatform implements SwlAsyncPlatform
{

    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var SwlDuplexShellOption
     */
    protected $option;

    public function __construct(
        Host $host,
        PlatformConfig $config,
        Shell $shell,
        SwlDuplexShellOption $option,
        LoggerInterface $logger
    )
    {
        $this->shell = $shell;
        $this->option = $option;
        parent::__construct($host, $config, $logger);
    }

    public function getAppId(): string
    {
        return $this->shell->getId();
    }

    protected function initServer() : Server
    {
        $serverOption = $this->option->serverOption;
        $server = new Server($serverOption->host, $serverOption->port);
        $server->set($serverOption->serverSettings);

        $table = new Swoole\Table($this->option->tableSize);
        $table->column('fd', Swoole\Table::TYPE_INT);
        $table->column('sessionId', Swoole\Table::TYPE_STRING, 32);
        $table->create();

        $server->table = $table;

        return $server;
    }

    protected function initProcess(Server $server) : Process
    {
        return new Process(function($process){

            Swoole\Coroutine\run(function() {
                /**
                 * @var Broadcaster $broadcaster
                 */
                $broadcaster = $this
                    ->host
                    ->getProcContainer()
                    ->make(Broadcaster::class);

                $broadcaster->subscribe(
                    [$this, 'receiveAsyncRequest'],
                    $this->shell->getId(),
                    null
                );

            });
        });
    }

    public function receiveAsyncRequest(string $chan, ShellOutputRequest $request) : void
    {
        $sessionId = $request->getSessionId();

        $this->isSessionExists($sessionId);
        $fd = $this->getSessionFd($sessionId);
        // fd 已经不存在了.
        if (empty($fd)) {
            $this->unsetSessionRoute($sessionId);
            return;
        }

        $packer = new TcpPacker(
            $this,
            $this->getServer(),
            $fd,
            '',
            $request
        );

        $this->onPacker(
            $packer,
            $this->option->adapterName,
            ShellOutputReqHandler::class
        );

        $packer->destroy();

    }

    public function getServer(): Server
    {
        return $this->server;
    }


    public function getServerTable() : Swoole\Table
    {
        return $this->server->table;
    }

    public function getSessionFd(string $sessionId) :  ? int
    {
        $sessionId = md5($sessionId);

        $table = $this->getServerTable();
        $data = $table->get($sessionId);
        return empty($data)
            ? null
            : $data['fd'];
    }

    public function getFdSession(int $fd) : ? string
    {
        $table = $this->getServerTable();
        $data = $table->get(strval($fd));
        return empty($data)
            ? null
            : $data['sessionId'];

    }

    public function isSessionExists(string $sessionId): bool
    {
        $sessionId = md5($sessionId);

        $table = $this->getServerTable();
        return $table->exists($sessionId);
    }


    public function setSessionRoute(string $sessionId, int $fd) : void
    {
        $sessionId = md5($sessionId);

        $table = $this->getServerTable();
        $data = ['fd' => $fd, 'sessionId' => $sessionId];
        $table->set($sessionId, $data);
        $table->set(strval($fd), $data);
    }

    public function unsetSessionRoute(string $sessionId) : void
    {
        $sessionId = md5($sessionId);
        $table = $this->getServerTable();
        $data = $table->get($sessionId);
        $fd = empty($data)
            ? null
            : $data['fd'];

        if (isset($fd)) {
            $table->del($sessionId);
            $table->del(strval($fd));
        }
    }

    public function serve(): void
    {
        Swoole\Runtime::enableCoroutine();

        // 创建Server对象
        $this->server = $this->initServer();

        // 添加进程.
        $process = $this->initProcess($this->server);
        $this->server->addProcess($process);

        // 监听数据接收事件
        $this->server->on(
            'Receive',
            function (Server $server, $fd, $from_id, $data) {

                $packer = new TcpPacker($this, $server, $fd, $from_id, $data);
                $this->onPacker(
                    $packer,
                    $this->option->adapterName,
                    ShellInputReqHandler::class
                );

                $packer->destroy();
            }
        );

        // 监听退出事件.
        $this->server->on(
            'Close',
            function($serv, $fd) {

                // 防止 table 被虚假路由塞满.

                $sessionId = $this->getFdSession($fd);
                if (isset($sessionId)) {
                    $this->unsetSessionRoute($sessionId);
                }
            }
        );

        // 启动服务器
        $this->server->start();
    }

    public function sleep(float $seconds): void
    {
        if (SwooleUtils::isInCoroutine()) {
            Swoole\Coroutine::sleep($seconds);
        }
    }

    public function shutdown(): void
    {
        if (isset($this->server)) {
            $this->server->shutdown();
        }
    }

    protected function handleRequest(Platform\Adapter $adapter, AppRequest $request, string $interface = null): void
    {
        $interface = $interface ?? ShellInputReqHandler::class;
        $response = $this->shell->handleRequest(
            $request,
            $interface
        );
        $adapter->sendResponse($response);
    }


}