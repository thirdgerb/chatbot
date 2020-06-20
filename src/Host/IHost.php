<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host;

use Commune\Framework\Bootstrap\LoadConfigOption;
use Commune\Framework\Bootstrap\RegisterProviders;
use Commune\Ghost\IGhost;
use Commune\Host\Bootstrap;
use Commune\Shell\IShell;
use Commune\Blueprint\Ghost;
use Commune\Blueprint\Host;
use Commune\Blueprint\Shell;
use Commune\Framework\AbsApp;
use Commune\Blueprint\Platform;
use Commune\Contracts\Log\LogInfo;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Blueprint\Configs\GhostConfig;
use Commune\Blueprint\Configs\HostConfig;
use Commune\Blueprint\Configs\PlatformConfig;
use Commune\Blueprint\Configs\ShellConfig;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\ServiceRegistry;
use Commune\Blueprint\Exceptions\Boot\AppNotDefinedException;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IHost extends AbsApp implements Host
{
    protected $bootstrappers = [
        // 读取配置
        Bootstrap\HostLoadConfigOption::class,
        // 注册服务
        Bootstrap\HostRegisterProviders::class,
    ];

    /**
     * @var HostConfig
     */
    protected $config;

    /**
     * @var null|GhostConfig
     */
    protected $ghostConfig;

    /**
     * @var null|ShellConfig
     */
    protected $shellConfig;

    /**
     * @var null|PlatformConfig
     */
    protected $platformConfig;

    public function __construct(
        HostConfig $config,
        ContainerContract $procC = null,
        ReqContainer $reqC = null,
        ServiceRegistry $registry = null,
        ConsoleLogger $consoleLogger = null,
        LogInfo $logInfo = null
    )
    {
        $this->config = $config;
        parent::__construct($procC, $reqC, $registry, $consoleLogger, $logInfo);
    }

    protected function basicBindings(): void
    {
        $this->instance(Host::class, $this);
        $this->instance(HostConfig::class, $this->config);
    }

    public function getId(): string
    {
        return $this->config->id;
    }

    public function getName(): string
    {
        return $this->config->name;
    }

    public function getConfig(): HostConfig
    {
        return $this->config;
    }

    public function run(string $platformId, callable $bootFailure = null): void
    {
        if (isset($bootFailure)) {
            $this->onFail($bootFailure);
        }

        // 获取配置.
        $platformConfig = $this->config->getPlatformConfig($platformId);

        if (empty($platformConfig)) {

            $ids = array_map(function(PlatformConfig $config) {
                return $config->id;
            }, $this->config->platforms);

            throw new AppNotDefinedException(
                'platform',
                $platformId,
                'available: ' . implode(',', $ids)
            );
        }

        $this->initPlatform($platformConfig);
        $this->initGhost($platformConfig);
        $this->initShell($platformConfig);

        $this->bootstrap();
        $this->activate();

        try {

            /**
             * @var Platform $platform
             */
            $platform = $this
                ->getProcContainer()
                ->make(Platform::class);

        } catch (\Throwable $e) {
            $this->getConsoleLogger()->critical(strval($e));
        }

        // 启动服务
        $platform->serve();
    }


    protected function initPlatform(PlatformConfig $config) : void
    {
        $this->platformConfig = $config;
        $this->instance(PlatformConfig::class, $config);

        $this->getProcContainer()->singleton(
            Platform::class,
            $config->concrete
        );
    }

    protected function initGhost(PlatformConfig $config) : void
    {
        $bootGhost = $config->bootGhost;

        if (!$bootGhost) {
            return;
        }

        $this->ghostConfig = $this->config->ghost;
        $this->instance(GhostConfig::class, $this->ghostConfig);

        $this->getProcContainer()->singleton(
            Ghost::class,
            function() {
                return new IGhost(
                    $this->ghostConfig,
                    $this->getProcContainer(),
                    $this->getBasicReqContainer(),
                    $this->getServiceRegistry(),
                    $this->getConsoleLogger(),
                    $this->getLogInfo()
                );
            }
        );
    }

    protected function initShell(PlatformConfig $config) : void
    {
        $bootShell = $config->bootShell;
        if (empty($bootShell)) {
            return;
        }

        $shellConfig = $this->config->getShellConfig($bootShell);
        if (empty($shellConfig)) {
            throw new AppNotDefinedException(
                'shell',
                $bootShell
            );
        }

        $this->shellConfig = $shellConfig;
        $this->instance(ShellConfig::class, $this->shellConfig);

        $this->getProcContainer()->singleton(
            Shell::class,
            function() {
                return new IShell(
                    $this->shellConfig,
                    $this->getProcContainer(),
                    $this->getBasicReqContainer(),
                    $this->getServiceRegistry(),
                    $this->getConsoleLogger(),
                    $this->getLogInfo()
                );
            }
        );
    }

    protected function doBootstrap(): void
    {
        parent::doBootstrap();

        $container = $this->getProcContainer();

        if (isset($this->platformConfig)) {

            // 配置加载.
            $registry = $this->getServiceRegistry();
            RegisterProviders::registerProviderByConfig(
                $registry,
                $this->platformConfig->providers
            );

            LoadConfigOption::registerConfigBinding(
                $this,
                $this->platformConfig->options
            );

        }

        if (isset($this->ghostConfig)) {
            $container->get(Ghost::class)->bootstrap();
        }

        if (isset($this->shellConfig)) {
            $container->get(Shell::class)->bootstrap();
        }
    }

    protected function doActivate(): void
    {
        parent::doActivate();

        $container = $this->getProcContainer();

        if (isset($this->ghostConfig)) {
            $container->get(Ghost::class)->activate();
        }

        if (isset($this->shellConfig)) {
            $container->get(Shell::class)->activate();
        }
    }

}