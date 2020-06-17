<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework;

use Commune\Blueprint\Exceptions\CommuneBootingException;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\ComponentOption;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\ServiceRegistry;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\LogInfo;
use Commune\Contracts\ServiceProvider;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IServiceRegistry implements ServiceRegistry
{

    /**
     * @var ContainerContract
     */
    protected $procC;

    /**
     * @var ContainerContract
     */
    protected $reqC;

    /**
     * @var ConsoleLogger
     */
    protected $consoleLogger;

    /**
     * @var LogInfo
     */
    protected $logInfo;

    /**
     * @var ServiceProvider[]
     */
    protected $configProviders = [];

    /**
     * @var ServiceProvider[]
     */
    protected $procProviders = [];

    /**
     * @var ServiceProvider[]
     */
    protected $reqProviders = [];


    /**
     * @var ComponentOption[]
     */
    protected $components = [];

    /**
     * @var array[]
     */
    protected $dependingComponents = [];

    /**
     * @var bool
     */
    protected $configBooted = false;

    /**
     * @var bool
     */
    protected $procBooted = false;

    /**
     * @var bool
     */
    protected $componentBooted = false;

    /**
     * IServiceRegistrar constructor.
     * @param ContainerContract $procC
     * @param ContainerContract $reqC
     * @param ConsoleLogger $consoleLogger
     * @param LogInfo $logInfo
     */
    public function __construct(
        ContainerContract $procC,
        ContainerContract $reqC,
        ConsoleLogger $consoleLogger,
        LogInfo $logInfo
    )
    {
        $this->procC = $procC;
        $this->reqC = $reqC;
        $this->consoleLogger = $consoleLogger;
        $this->logInfo = $logInfo;
    }


    public function registerConfigProvider(
        ServiceProvider $provider,
        bool $top
    ): void
    {
        $provider->register($this->procC);
        $this->checkProviderScope($provider, ServiceProvider::SCOPE_CONFIG);
        $this->registerProvider($this->configProviders, $provider, $top);
    }

    protected function checkProviderScope(ServiceProvider $provider, string $actual) : void
    {
        $expect = $provider->getDefaultScope();
        if ($expect !== $actual) {
            $this->consoleLogger->warning(
                $this->logInfo->bootingRegisterProviderWarning(
                    $provider->getId(),
                    $expect,
                    $actual
                )
            );
        }
    }

    public function registerProcProvider(
        ServiceProvider $provider,
        bool $top
    ): void
    {
        $provider->register($this->procC);
        $this->checkProviderScope($provider, ServiceProvider::SCOPE_PROC);
        $this->registerProvider($this->procProviders, $provider, $top);
    }

    public function registerReqProvider(
        ServiceProvider $provider,
        bool $top
    ): void
    {
        $provider->register($this->reqC);
        $this->checkProviderScope($provider, ServiceProvider::SCOPE_REQ);
        $this->registerProvider($this->reqProviders, $provider, $top);
    }

    protected function registerProvider(
        array &$providers,
        ServiceProvider $provider,
        bool $top
    ) : void
    {
        $id = $provider->getId();
        if (isset($providers[$id])) {
            $this->consoleLogger->warning(
                $this->logInfo->bootingRegisterExistsProvider($id)
            );
        }

        // 按顺序注册
        if ($top) {
            $providers = [$id => $provider] + $providers;
        } else {
            $providers[$id] = $provider;
        }

        // 日志
        $this->consoleLogger->debug(
            $this->logInfo->bootingRegisterProvider($id)
        );
    }

    public function getConfigProviders(): array
    {
        return $this->configProviders;
    }

    public function getProcProviders(): array
    {
        return $this->procProviders;
    }

    public function getReqProviders(): array
    {
        return $this->reqProviders;
    }

    public function bootConfigServices(): bool
    {
        if ($this->configBooted) {
            return false;
        }

        foreach ($this->configProviders as $id => $provider) {
            $provider->boot($this->procC);
            // 初始化服务
            $this->consoleLogger->debug(
                $this->logInfo->bootingBootProvider($id)
            );
        }

        return $this->configBooted = true;
    }

    public function bootProcServices(): bool
    {
        if ($this->procBooted) {
            return false;
        }

        foreach ($this->procProviders as $id => $provider) {
            $provider->boot($this->procC);
            // 初始化服务
            $this->consoleLogger->debug(
                $this->logInfo->bootingBootProvider($id)
            );
        }

        return $this->procBooted = true;
    }

    public function bootReqServices(ReqContainer $container): bool
    {
        if (!$container->isInstanced()) {
            throw new CommuneBootingException(
                $this->logInfo->bootingUnInstancedReqContainer()
            );
        }

        if ($container->isBooted()) {
            return false;
        }

        // 也不记录日志了.
        foreach ($this->reqProviders as $id => $provider) {
            $provider->boot($container);
        }
        $container->booted();
        return true;
    }


    public function isConfigServicesBooted(): bool
    {
        return $this->configBooted;
    }

    public function isProcServicesBooted(): bool
    {
        return $this->procBooted;
    }

    public function isComponentsBooted(): bool
    {
        return $this->componentBooted;
    }


    public function registerComponent(
        ComponentOption $componentOption,
        string $by = null,
        bool $force = false
    ) : bool
    {
        $id = $componentOption->getId();

        if (
            isset($this->components[$id])
            && !$force
        ) {
           return false;
        }

        $this->components[$id] = $componentOption;
        $this->consoleLogger->debug(
            $this->logInfo->bootingRegisterComponent($id, $by)
        );
        return true;
    }

    public function dependComponent(
        string $by,
        string $componentName,
        array $params = []
    ): bool
    {
        if (isset($this->dependingComponents[$componentName])) {
            return false;
        }

        $this->dependingComponents[$componentName] = [$by, $params];
        return true;
    }


    public function bootComponents(App $app): void
    {
        if ($this->componentBooted) {
            return;
        }

        // 先注册依赖组件.
        foreach ($this->dependingComponents as $name => list($by, $params)) {
            $component = new $name($params);
            $this->registerComponent($component, $by, false);
            unset($component);
        }

        // 启动所有的组件.
        foreach ($this->components as $id => $component) {
            $component->bootstrap($app);
            $this->consoleLogger->debug(
                $this->logInfo->bootingBootComponent(
                    get_class($app),
                    $id
                )
            );
        }
        $this->componentBooted = true;
    }


}