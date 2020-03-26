<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype;

use Commune\Container\ContainerContract;
use Commune\Framework\Blueprint\App;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Contracts\Cache;
use Commune\Framework\Contracts\ExceptionReporter;
use Commune\Framework\Contracts\LogInfo;
use Commune\Framework\Contracts\Messenger;
use Commune\Framework\Contracts\Server;
use Commune\Framework\Contracts\ServiceProvider;
use Commune\Framework\Exceptions\BootingException;
use Commune\Framework\Prototype\Log\IConsoleLogger;
use Commune\Framework\Prototype\Log\ILogInfo;
use Commune\Support\Babel\Babel;
use Psr\Log\LoggerInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class AbsApp implements App
{

    /**
     * @var ContainerContract
     */
    protected $procContainer;

    /**
     * @var ReqContainer
     */
    protected $reqContainer;

    /*------ providers ------*/

    /**
     * @var ServiceProvider[]
     */
    protected $procProviders = [];

    /**
     * @var ServiceProvider[]
     */
    protected $reqProviders = [];

    /*------ cached ------*/

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var LogInfo
     */
    protected $logInfo;

    /**
     * @var LoggerInterface
     */
    protected $consoleLogger;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var bool
     */
    protected $procBooted = false;

    public function __construct(
        ContainerContract $procContainer,
        ReqContainer $reqContainer,
        bool $debug = false,
        LogInfo $logInfo = null,
        LoggerInterface $consoleLogger = null
    )
    {
        $this->procContainer = $procContainer;
        $this->reqContainer = $reqContainer;
        $this->debug = $debug;
        $this->logInfo = $logInfo ?? new ILogInfo();
        $this->consoleLogger = $consoleLogger ?? new IConsoleLogger($this->debug);
    }

    public function isDebugging(): bool
    {
        return $this->isDebugging();
    }


    public function getServer(): Server
    {
        return $this->server ?? $this->server = $this->procContainer->get(Server::class);
    }

    public function getCache(): Cache
    {
        return $this->procContainer->get(Cache::class);
    }

    public function getBabel(): Babel
    {
        return $this->procContainer->get(Cache::class);
    }

    public function getMessenger(): Messenger
    {
        return $this->procContainer->get(Messenger::class);
    }

    public function getExceptionReporter(): ExceptionReporter
    {
        return $this->procContainer->get(ExceptionReporter::class);
    }

    public function getReqContainer(): ReqContainer
    {
        return $this->reqContainer;
    }

    public function getProcContainer(): ContainerContract
    {
        return $this->procContainer;
    }

    public function registerProvider(
        string $serviceProvider,
        array $data = [],
        bool $top = false
    ): void
    {
        // 检查是不是正确的 provider
        if (!is_a($serviceProvider, ServiceProvider::class, true)) {
            throw new BootingException(
                $this->getLogInfo()->bootRegisterInvalidProvider($serviceProvider)
            );
        }

        /**
         * @var ServiceProvider $provider
         */
        $provider = new $serviceProvider($data);




    }

    public function registerProviderIns(
        ServiceProvider $provider,
        bool $top
    ): void
    {

    }


    public function bootProcServices(): void
    {
        if ($this->procBooted) {
            return;
        }

        foreach ($this->procProviders as $provider) {
            $provider->boot($this->procContainer);
        }

        $this->procBooted = true;
    }

    public function bootReqServices(ReqContainer $container): void
    {
        foreach ($this->reqProviders as $provider) {
            $provider->boot($container);
        }
    }

    public function getLogger(): LoggerInterface
    {
        return $this->procContainer->get(LoggerInterface::class);
    }

    public function getLogInfo(): LogInfo
    {
        return $this->logInfo;
    }

    public function getConsoleLogger(): LoggerInterface
    {
        return $this->consoleLogger;
    }


}