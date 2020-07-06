<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Shell\Stdio;

use Clue\React\Stdio\Stdio;
use Commune\Blueprint\Kernel\Handlers\ShellInputReqHandler;
use Commune\Blueprint\Platform;
use Commune\Blueprint\Shell;
use Commune\Contracts\Cache;
use Commune\Contracts\Messenger\Broadcaster;
use Commune\Platform\AbsPlatform;
use Commune\Platform\Libs\Stdio\StdioClientOption;
use Commune\Platform\Libs\Stdio\StdioPacker;
use Psr\Log\LoggerInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Swoole\Coroutine;
use Commune\Blueprint\Host;
use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Kernel\Handlers\ShellOutputReqHandler;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioShellPlatform extends AbsPlatform
{

    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var StdioClientOption
     */
    protected $option;

    /**
     * @var Stdio
     */
    protected $stdio;

    /**
     * @var LoopInterface
     */
    protected $loop;

    public function __construct(
        Host $host,
        PlatformConfig $config,
        Shell $shell,
        StdioClientOption $option,
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

    public function serve(): void
    {
        Coroutine\run(function(){

            $this->loop = Factory::create();
            $this->stdio = new Stdio($this->loop);

            $this->stdio->setPrompt('> ');
            $packer = $this->makePacker('');

            // 发送启动信息.
            $this->onPacker(
                $packer,
                $this->option->adapter,
                ShellInputReqHandler::class
            );

            /**
             * @var Broadcaster $broadcaster
             */
            $broadcaster = $this->host->getProcContainer()->get(Broadcaster::class);
            Coroutine::create(
                function (
                    Broadcaster $broadcaster,
                    string $sessionId
                ){
                    $broadcaster->subscribe(
                        [$this, 'subscribe'],
                        $this->getAppId(),
                        $sessionId
                    );
                },
                $broadcaster,
                $packer->sessionId
            );

            // 处理同步请求.
            $this->stdio->on('data', function($line) {
                $packer = $this->makePacker($line);

                $continue = $this->onPacker(
                    $packer,
                    $this->option->adapter,
                    ShellInputRequest::class
                );

                if (!$continue) {
                    $this->loop->stop();
                }
            });

            $this->loop->run();
        });
    }

    public function sleep(float $seconds): void
    {
        Coroutine::sleep($seconds);
    }

    public function shutdown(): void
    {
        exit(0);
    }

    public function subscribe(string $chan, ShellOutputRequest $request) : void
    {
        $packer = $this->makePacker('');
        $adapter = $packer->adapt($this->option->adapter, $this->getAppId());

        $response = $this->shell->handleRequest(
            $request,
            ShellOutputReqHandler::class
        );
        $adapter->sendResponse($response);

        $adapter->destroy();
        $packer->destroy();
    }

    protected function handleRequest(
        Platform\Adapter $adapter,
        AppRequest $request,
        string $interface = null
    ): void
    {
        $response = $this->shell->handleRequest(
            $request,
            $interface
        );
        $adapter->sendResponse($response);
    }

    public function makePacker(string $line) : StdioPacker
    {
        return new StdioPacker(
            $this->stdio,
            $this,
            $this->option->creatorName,
            $line
        );
    }

}