<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\NLU\TNTSearch\Platform;

use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Framework\ProcContainer;
use Commune\Blueprint\Host;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Platform;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Platform\AbsPlatform;
use Psr\Log\LoggerInterface;
use Swoole\Coroutine;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TNTSearchSwlCoTcpPlatform implements Platform
{

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

    /**
     * @var ProcContainer
     */
    protected $procC;


    public function getId(): string
    {
        return $this->config->id;
    }

    public function getAppId(): string
    {
        return $this->getAppId();
    }

    public function getConfig(): PlatformConfig
    {
        return $this->config;
    }

    public function getContainer(): ProcContainer
    {
        return $this->procC;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function serve(): void
    {
        try {

        $server = new Coroutine\Server('127.0.0.1', '9501' , false, true);

        \Swoole\Process::signal(SIGTERM, function () use ($server) {
            $server->shutdown();
        });

            $server->handle([$this, 'receive']);

        } catch (\Throwable $e) {
            $this->catchExp($e);
            $this->shutdown();
        }
    }

    public function receive(Coroutine\Server\Connection $conn) : void
    {
        try {
            while (true) {
                $data = $conn->recv();

                if (empty($data)) {
                    $conn->close();
                    break;
                }
                $this->handle($conn, $data);
            }

        } catch (\Throwable $e) {
            $this->catchExp($e);
        }
    }




    public function sleep(float $seconds): void
    {
        Coroutine::sleep($seconds);
    }

    public function shutdown(): void
    {
        swoole_event_wait();

    }

    public function catchExp(\Throwable $e): void
    {
        $this->host->getProcContainer()->get(ExceptionReporter::class)->report($e);
    }


}