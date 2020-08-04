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
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Swoole\Runtime;

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
        parent::__construct($host, $config, $logger);

        $this->shell = $shell;
        $this->option = $option;
    }

    public function getAppId(): string
    {
        return $this->shell->getId();
    }

    public function serve(): void
    {
        Runtime::enableCoroutine(true);

        Coroutine\run([$this, 'run']);
    }

    public function run() : void
    {

        $this->loop = Factory::create();
        $this->stdio = new Stdio($this->loop);

        $this->stdio->setPrompt('> ');
        $packer = $this->makePacker('#connect');

        // 发送启动信息.
        $this->onPacker(
            $packer,
            $this->option->adapter,
            ShellInputReqHandler::class
        );

        Coroutine::create(
            function (string $sessionId){
                /**
                 * @var Broadcaster $broadcaster
                 */
                $broadcaster = $this->host->getProcContainer()->get(Broadcaster::class);

                $broadcaster->subscribe(
                    [$this, 'subscribe'],
                    $this->getAppId(),
                    $sessionId
                );
            },
            $packer->sessionId
        );

        // 处理同步请求.
        $this->stdio->on('data', function($line) {
            $packer = $this->makePacker($line);

            $continue = $this->onPacker(
                $packer,
                $this->option->adapter,
                ShellInputReqHandler::class
            );

            $packer->destroy();

            if (!$continue) {
                $this->shutdown();
            }
        });

        $this->loop->run();
    }

    public function sleep(float $seconds): void
    {
        Coroutine::sleep($seconds);
    }

    public function shutdown(): void
    {
        $this->stdio->write("end stdio loop. please exit process manually");
        $this->stdio->end();
    }

    public function subscribe(string $chan, ShellOutputRequest $request) : void
    {
        $packer = $this->makePacker('');
        $adapter = $packer->adapt($this->option->adapter, $this->getAppId());

        $this->onAdapter(
            $packer,
            $adapter,
            ShellOutputReqHandler::class,
            $request
        );

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

    /**
     * @param StdioPacker $packer
     * @param string|null $error
     * @return bool
     */
    protected function donePacker(Platform\Packer $packer, string $error = null): bool
    {
        if ($packer->quit) {
            return false;
        }
        return parent::donePacker($packer, $error);
    }
}