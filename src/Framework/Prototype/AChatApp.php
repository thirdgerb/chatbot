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
use Commune\Framework\Blueprint\ChatApp;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Contracts\Bootstrapper;
use Commune\Framework\Contracts\Cache;
use Commune\Framework\Contracts\ExceptionReporter;
use Commune\Framework\Contracts\LogInfo;
use Commune\Framework\Contracts\Messenger;
use Commune\Framework\Contracts\Server;
use Commune\Framework\Contracts\ServiceProvider;
use Commune\Framework\Contracts\ConsoleLogger;
use Commune\Framework\Exceptions\BootingException;
use Commune\Framework\Prototype\Log\IConsoleLogger;
use Commune\Framework\Prototype\Log\ILogInfo;
use Commune\Support\Babel\BabelResolver;
use Psr\Log\LoggerInterface;

/**
 * Abstract Application
 *`
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AChatApp implements ChatApp
{

    /*------ configure ------*/

    /**
     * 启动
     * @var string[] is_a Bootstrapper
     */
    protected $bootstrappers = [];

    /*------ properties ------*/

    /**
     * @var ContainerContract
     */
    protected $procContainer;

    /**
     * @var ReqContainer
     */
    protected $reqContainer;

    /**
     * @var LogInfo
     */
    protected $logInfo;

    /**
     * @var ConsoleLogger
     */
    protected $consoleLogger;

    /**
     * @var bool
     */
    protected $debug;

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
     * @var bool
     */
    protected $booted = false;

    /**
     * @var bool
     */
    protected $procBooted = false;

    public function __construct(
        ContainerContract $procContainer,
        ReqContainer $reqContainer,
        bool $debug = false,
        LogInfo $logInfo = null,
        ConsoleLogger $consoleLogger = null
    )
    {
        $this->procContainer = $procContainer;
        $this->reqContainer = $reqContainer;
        $this->debug = $debug;
        $this->logInfo = $logInfo ?? new ILogInfo();
        $this->consoleLogger = $consoleLogger ?? new IConsoleLogger($this->debug);
        $this->basicBinding();
    }

    protected function basicBinding() : void
    {
        // 绑定日志
        $this->procContainer->instance(LogInfo::class, $this->logInfo);
        $this->reqContainer->instance(LogInfo::class, $this->logInfo);

        $this->procContainer->instance(ConsoleLogger::class, $this->consoleLogger);
        $this->reqContainer->instance(ConsoleLogger::class, $this->consoleLogger);

        // 绑定自己
        $this->procContainer->instance(ChatApp::class, $this);
        $this->reqContainer->instance(ChatApp::class, $this);

        // 绑定 ReqContainer 的基本单例.
        $container = $this->reqContainer;

        // 绑定 Server
        $container->singleton(Server::class, function(ContainerContract $ioc) {
            /**
             * @var ChatApp $app
             */
            $app = $ioc->get(ChatApp::class);
            return $app->getServer();
        });

        // 日志是请求级单例. 是否是进程级单例, 取决于日志底层是否实现好了协程等非阻塞机制.
        $container->singleton(LoggerInterface::class, function(ContainerContract $ioc){
            /**
             * @var ChatApp $app
             */
            $app = $ioc->get(ChatApp::class);
            return $app->getLogger();
        });

        // 绑定 cache 为请求级单例
        $container->singleton(Cache::class, function(ContainerContract $ioc){
            /**
             * @var ChatApp $app
             */
            $app = $ioc->get(ChatApp::class);
            return $app->getCache();
        });

        // 绑定 Messenger 为请求级单例
        $container->singleton(Messenger::class, function(ContainerContract $ioc){
            /**
             * @var ChatApp $app
             */
            $app = $ioc->get(ChatApp::class);
            return $app->getMessenger();
        });

        // 绑定 ExceptionReporter 为请求级单例.
        $container->singleton(ExceptionReporter::class, function(ContainerContract $ioc){
            /**
             * @var ChatApp $app
             */
            $app = $ioc->get(ChatApp::class);
            return $app->getExceptionReporter();
        });

    }

    public function isDebugging(): bool
    {
        return $this->isDebugging();
    }

    public function bootstrap(): void
    {
        if ($this->booted) {
            return;
        }

        try {
            $method = static::class . '::' . __FUNCTION__;
            $this->consoleLogger->info(
                $this->logInfo->bootStartKeyStep($method)
            );

            foreach ($this->bootstrappers as $bootstrapperName) {

                $this->consoleLogger->info(
                    $this->logInfo->bootStartKeyStep($bootstrapperName)
                );

                /**
                 * @var Bootstrapper $bootstrapper
                 */
                $bootstrapper = $this->getProcContainer()->get($bootstrapperName);
                $bootstrapper->bootstrap();

                $this->consoleLogger->info(
                    $this->logInfo->bootEndKeyStep($bootstrapperName)
                );
            }

            $this->consoleLogger->info(
                $this->logInfo->bootEndKeyStep($method)
            );
            $this->booted = true;

        } catch (\Throwable $e) {
            $this->consoleLogger->critical(strval($e));
            exit(255);
        }
    }


    public function getServer(): Server
    {
        // 先进行完全初始化.
        $this->bootstrap();
        // 获取 Server 单例.
        return $this->server ?? $this->server = $this->procContainer->get(Server::class);
    }

    public function getCache(): Cache
    {
        return $this->procContainer->get(Cache::class);
    }

    public function getBabel(): BabelResolver
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

    public function newReqContainerInstance(string $id): ReqContainer
    {
        $container = $this->getReqContainer()->newInstance($id, $this->getProcContainer());

        // 绑定 reqContainer
        $container->share(ReqContainer::class, $container);

        return $container;
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
        $this->registerProviderIns($provider, $top);
    }

    public function registerProviderIns(
        ServiceProvider $provider,
        bool $top
    ): void
    {
        if ($provider->isProcessServiceProvider()) {
            $this->procProviders = $this->mergeProvider($provider, $this->procProviders, $top);
        } else {
            $this->reqProviders = $this->mergeProvider($provider, $this->reqProviders, $top);
        }
    }

    protected function mergeProvider(ServiceProvider $provider, array $providers, bool $top) : array
    {
        $id = $provider->getId();
        if (array_key_exists($id, $providers)) {
            $this->consoleLogger
                ->warning(
                    $this->logInfo->bootRegisterExistsProvider($id)
                );

            $providers[$id] = $provider;
            return $providers;
        }

        $this->consoleLogger
            ->debug(
                $this->logInfo->bootRegisterProvider($id)
            );


        if ($top) {

            return array_merge([$id => $provider], $providers);

        } else {

            $providers[$id] = $provider;
            return $providers;
        }
    }

    public function bootProcServices(): void
    {
        if ($this->procBooted) {
            return;
        }

        $this->consoleLogger->info(
            $this->logInfo->bootStartKeyStep(__METHOD__)
        );

        // 注册所有的服务
        foreach ($this->procProviders as $provider) {
            $provider->register($this->procContainer);
        }

        foreach ($this->reqProviders as $provider) {
            $provider->register($this->reqContainer);
        }

        // 初始化进程级服务.
        foreach ($this->procProviders as $provider) {
            $provider->boot($this->procContainer);
        }

        $this->procBooted = true;
        $this->consoleLogger->info(
            $this->logInfo->bootEndKeyStep(__METHOD__)
        );
    }

    public function bootReqServices(ReqContainer $container): void
    {
        // 不必每个请求都进行初始化.
        // 通常是 kernel 判断请求合法之后, 才需要进行初始化.

        foreach ($this->reqProviders as $provider) {
            $provider->boot($container);
        }
    }

    public function getProcProviders(): array
    {
        return $this->procProviders;
    }

    public function getReqProviders(): array
    {
        return $this->reqProviders;
    }


    public function getLogger(): LoggerInterface
    {
        return $this->procContainer->get(LoggerInterface::class);
    }

    public function getLogInfo(): LogInfo
    {
        return $this->logInfo;
    }

    public function getConsoleLogger(): ConsoleLogger
    {
        return $this->consoleLogger;
    }


}