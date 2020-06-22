<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Stdio;

use Commune\Blueprint\Platform;
use Clue\React\Stdio\Stdio;
use Commune\Blueprint\Host;
use Commune\Blueprint\Shell;
use Commune\Message\Host\Convo\IEventMsg;
use Commune\Message\Host\Convo\IText;
use Commune\Platform\AbsPlatform;
use Commune\Platform\PlatformHandler;
use Commune\Protocals\HostMsg\Convo\EventMsg;
use Commune\Support\Utils\TypeUtils;
use Psr\Log\LoggerInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Kernel\Protocals\AppRequest;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class StdioPlatform extends AbsPlatform
{
    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var StdioOption
     */
    protected $option;

    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var Stdio
     */
    protected $stdio;

    /**
     * @var StdioConsole
     */
    protected $writer;

    public function __construct(
        Host $host,
        PlatformConfig $config,
        Shell $shell,
        StdioOption $option,
        LoggerInterface $logger
    )
    {
        $this->shell = $shell;
        $this->option = $option;
        parent::__construct($host, $config, $logger);

        $this->loop = Factory::create();
        $this->stdio = new Stdio($this->loop);
        // stdio 对话中不要显示消息级别.
        $this->writer = new StdioConsole($this->stdio, false);
    }

    public function serve(): void
    {
        $this->stdio->setPrompt('> ');

        // each message
        $this->stdio->on('data', function($line) {
            $packer = new StdioPacker($this, IText::instance($line));
            $this->onPacker($packer);
        });

        // 启动连接.
        $packer = new StdioPacker(
            $this,
            IEventMsg::instance(EventMsg::SYSTEM_EVENT_CONNECTION)
        );
        $this->onPacker($packer);

        $this->loop->run();
    }


    public function getStdio() : Stdio
    {
        return $this->stdio;
    }

    public function getWriter() : StdioConsole
    {
        return $this->writer;
    }

    protected function handleRequest(Platform\Adapter $adapter, AppRequest $request): void
    {
        $shell = $this->getShell();

        $handled = PlatformHandler::shellHandleRequest(
            $shell,
            $adapter,
            $request
        );


        if (!$handled) {

            $adapterType = TypeUtils::getType($adapter);
            $requestType = TypeUtils::getType($request);

            throw new InvalidArgumentException(
                "adapter $adapterType, request $requestType"
            );
        }
    }


    public function getOption() : StdioOption
    {
        return $this->option;
    }

    public function isSessionAvailable(string $sessionId): bool
    {
        return true;
    }

    public function setSessionAvailable(bool $available): void
    {
    }

    public function sleep(float $seconds): void
    {
        usleep($seconds * 1000000);
    }

    public function shutdown(): void
    {
        exit;
    }


    public function getShell(): Shell
    {
        return $this->shell;
    }


}