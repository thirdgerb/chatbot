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

use React\EventLoop\Factory;
use Clue\React\Stdio\Stdio;
use Commune\Blueprint\Host;
use Commune\Blueprint\Shell;
use Psr\Log\LoggerInterface;
use Commune\Blueprint\Platform;
use Commune\Platform\AbsPlatform;
use React\EventLoop\LoopInterface;
use Commune\Platform\Libs\Stdio\StdioPacker;
use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Handlers\ShellInputReqHandler;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Contracts\Messenger\GhostMessenger;
use Commune\Platform\Libs\Stdio\StdioTextAdapter;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioConsolePlatform extends AbsPlatform
{
    /**
     * @var Shell
     */
    protected $shell;

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
        LoggerInterface $logger
    )
    {
        $this->shell = $shell;
        parent::__construct($host, $config, $logger);

        $this->loop = Factory::create();
        $this->stdio = new Stdio($this->loop);
    }

    protected function handleRequest(Platform\Adapter $adapter, AppRequest $request, string $interface = null): void
    {
        $interface = $interface ?? ShellInputReqHandler::class;

        $response = $this->shell->handleRequest(
            $request,
            ShellInputReqHandler::class
        );
        $adapter->sendResponse($response);
    }

    public function getAppId(): string
    {
        return $this->shell->getId();
    }

    public function serve(): void
    {

        $this->stdio->setPrompt('> ');

        $this->host->instance(
            GhostMessenger::class,
            $this->makeGhostMessenger()
        );

        $initPacker = $this->makePacker('');
        $this->onPacker($initPacker, StdioTextAdapter::class);
        unset($initPacker);


        $this->stdio->on('data', function($line) {

            $packer = $this->makePacker($line);
            $this->onPacker($packer, StdioTextAdapter::class);
        });

        $this->loop->run();

    }

    protected function makePacker(string $line) : StdioPacker
    {
        $id = $this->getId();
        return new StdioPacker(
            $this->stdio,
            $this,
            md5($id),
            $id,
            $line
        );

    }

    public function sleep(float $seconds): void
    {
        usleep($seconds * 1000000);
    }

    public function shutdown(): void
    {
        $this->stdio->end(__METHOD__);
    }

    protected function makeGhostMessenger() : GhostMessenger
    {
        return new class() implements GhostMessenger {

            public function asyncSendRequest(GhostRequest $request, GhostRequest ...$requests): void
            {
                return;
            }

            public function receiveAsyncRequest(): ? GhostRequest
            {
                return null;
            }
        };
    }

}