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

use Commune\Blueprint\Ghost;
use Commune\Blueprint\Kernel\Handlers\GhostRequestHandler;
use Commune\Blueprint\Kernel\Handlers\ShellOutputReqHandler;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Contracts\Messenger\Broadcaster;
use Commune\Support\Babel\Babel;
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
        parent::__construct($host, $config, $logger);

        $this->shell = $shell;
        $this->loop = Factory::create();
        $this->stdio = new Stdio($this->loop);
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

    public function getAppId(): string
    {
        return $this->shell->getId();
    }

    public function serve(): void
    {
        $this->stdio->setPrompt('> ');

        $initPacker = $this->makePacker('#connect');
        $this->onPacker($initPacker, StdioTextAdapter::class);
        unset($initPacker);

        $this->stdio->on('data', function($line) {

            $packer = $this->makePacker($line);
            $success = $this->onPacker($packer, StdioTextAdapter::class);

            $this->runAsyncInput();
            $this->runSubscribe();

            if (!$success) {
                $this->shutdown();
            }
        });

        $this->loop->run();
    }

    protected function runSubscribe() : void
    {
        /**
         * @var Broadcaster $broadcaster
         */
        $broadcaster = $this->host
            ->getProcContainer()
            ->make(Broadcaster::class);

        $broadcaster->subscribe(
            function($chan, ShellOutputRequest $request) {
                $packer = $this->makePacker('');
                $adapter = $packer->adapt(
                    StdioTextAdapter::class,
                    $this->shell->getId()
                );

                $this->onAdapter(
                    $packer,
                    $adapter,
                    ShellOutputReqHandler::class,
                    $request
                );
            },
            $this->shell->getId()
        );

    }

    protected function runAsyncInput() : void
    {
        /**
         * @var GhostMessenger $ghostMessenger
         */
        $ghostMessenger = $this->host
            ->getProcContainer()
            ->make(GhostMessenger::class);

        /**
         * @var Ghost $ghost
         */
        $ghost = $this->host->getProcContainer()->make(Ghost::class);
        while ($request = $ghostMessenger->receiveAsyncRequest()) {
            $ghost->handleRequest(
                $request,
                GhostRequestHandler::class
            );
        }
    }



    protected function makePacker(string $line) : StdioPacker
    {
        $id = $this->getId();
        return new StdioPacker(
            $this->stdio,
            $this,
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
        swoole_event_wait();
        $this->stdio->end();
    }

    /**
     * @param StdioPacker $packer
     * @param string|null $error
     * @return bool
     */
    protected function donePacker(Platform\Packer $packer, string $error = null): bool
    {
        $quit = $packer->quit;
        if ($quit) {
            return false;
        }
        return parent::donePacker($packer, $error);
    }
}