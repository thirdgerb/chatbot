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

use Swoole;
use Commune\Blueprint\Framework\ProcContainer;
use Commune\Blueprint\Platform;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Contracts\Messenger\Broadcaster;
use Commune\Support\Swoole\SwooleUtils;
use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Host;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Kernel\Handlers\ShellOutputReqHandler;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Shell;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwlBroadcastShellPlatform implements Platform
{

    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var Host
     */
    protected $host;

    /**
     * @var PlatformConfig
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        Host $host,
        PlatformConfig $config,
        Shell $shell,
        LoggerInterface $logger
    )
    {
        $this->shell = $shell;
        $this->host = $host;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function getAppId(): string
    {
        return $this->shell->getId();
    }

    public function receiveAsyncRequest(string $chan, ShellOutputRequest $request) : void
    {
        /**
         * @var ShellOutputResponse $response
         */
        $response = $this->shell->handleRequest($request, ShellOutputReqHandler::class);

        $traceId = $response->getTraceId();
        $console = $this->host->getConsoleLogger();
        $console->info("## $traceId");

        $outputs = $response->getOutputs();
        foreach ($outputs as $output) {
            $console->info($output->getMsgText());
        }
    }


    public function serve(): void
    {
        Swoole\Runtime::enableCoroutine();

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
    }

    public function sleep(float $seconds): void
    {
        if (SwooleUtils::isInCoroutine()) {
            Swoole\Coroutine::sleep($seconds);
        }
    }

    public function shutdown(): void
    {
        swoole_event_wait();
        exit;
    }

    public function getId(): string
    {
        return $this->config->id;
    }

    public function getConfig(): PlatformConfig
    {
        return $this->config;
    }

    public function getContainer(): ProcContainer
    {
        return $this->host->getProcContainer();
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function catchExp(\Throwable $e): void
    {
        /**
         * @var ExceptionReporter $reporter
         */
        $reporter = $this->getContainer()->make(ExceptionReporter::class);
        $reporter->report($e);
    }


}