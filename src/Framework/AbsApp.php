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

use Psr\Log\LogLevel;
use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Framework\ProcContainer;
use Commune\Framework\Bootstrap;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\Bootstrapper;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\ServiceRegistry;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\LogInfo;
use Commune\Framework\Log\IConsoleLogger;
use Commune\Framework\Log\ILogInfo;
use Commune\Framework\Spy\SpyAgency;
use Commune\Blueprint\Exceptions\Logic\InvalidConfigException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsApp implements App
{

    /**
     * @var string[]
     */
    protected $bootstrappers = [];

    /**
     * @var ContainerContract
     */
    protected $procC;

    /**
     * @var ReqContainer
     */
    protected $reqC;

    /**
     * @var ServiceRegistry
     */
    protected $serviceRegistry;

    /**
     * @var ConsoleLogger
     */
    protected $console;

    /**
     * @var LogInfo
     */
    protected $logInfo;

    /**
     * @var bool
     */
    protected $activated;

    /**
     * @var bool
     */
    protected $ranBootstrap = false;

    /**
     * @var callable|null
     */
    protected $fail;

    /**
     * AbsApp constructor.
     * @param ProcContainer|null $procC
     * @param ReqContainer|null $reqC
     * @param ServiceRegistry|null $registry
     * @param ConsoleLogger|null $consoleLogger
     * @param LogInfo|null $logInfo
     */
    public function __construct(
        ProcContainer $procC = null,
        ReqContainer $reqC = null,
        ServiceRegistry $registry = null,
        ConsoleLogger $consoleLogger = null,
        LogInfo $logInfo = null
    )
    {
        $this->procC = $procC ?? new IProcContainer();

        $this->reqC = $reqC ?? new IReqContainer($this->procC);

        $startLevel = LogLevel::DEBUG ;
        $this->console = $consoleLogger ?? new IConsoleLogger(true, $startLevel);

        $this->logInfo = $logInfo ?? new ILogInfo();

        $this->serviceRegistry = $registry ?? new IServiceRegistry(
            $this->procC,
            $this->reqC,
            $this->console,
            $this->logInfo
        );

        // 不怕重复绑定.
        $this->instance(ProcContainer::class, $this->procC);
        $this->instance(ReqContainer::class, $this->reqC);
        $this->instance(ConsoleLogger::class, $this->console);
        $this->instance(LogInfo::class, $this->logInfo);
        $this->instance(ServiceRegistry::class, $this->serviceRegistry);

        // 默认绑定关系.
        $this->basicBindings();

        SpyAgency::incr(static::class);
    }

    public function instance($abstract, $instance) : void
    {
        $this->procC->instance($abstract, $instance);
        $this->reqC->instance($abstract, $instance);
    }

    /*------- abstract -------*/

    abstract protected function basicBindings() : void;

    public function newReqContainerIns(string $uuid): ReqContainer
    {
        $container = $this->reqC->newInstance($uuid, $this->procC);
        $container->share(ReqContainer::class, $container);
        return $container;
    }

    /*------ getter ------*/


    public function getProcContainer(): ProcContainer
    {
        return $this->procC;
    }

    public function getBasicReqContainer(): ReqContainer
    {
        return $this->reqC;
    }

    public function getServiceRegistry(): ServiceRegistry
    {
        return $this->serviceRegistry;
    }

    public function getConsoleLogger(): ConsoleLogger
    {
        return $this->console;
    }

    public function getLogInfo(): LogInfo
    {
        return $this->logInfo;
    }

    public function onFail(callable $fail): App
    {
        $this->fail = $fail;
        return $this;
    }


    /**
     * 项目总启动.
     * @return App
     */
    public function activate(): App
    {
        try {
            $this->bootstrap();
            $this->doActivate();

        } catch (\Throwable $e) {
            $this->console->emergency(strval($e));
            $this->fail();
        }

        return $this;
    }

    protected function doActivate() : void
    {
        if ($this->activated) {
            return;
        }

        $registrar = $this->getServiceRegistry();

        // 检查是否已经激活过了.
        $activated = $this->activated
            ?? $this->activated = $registrar->isComponentsBooted()
                // 配置相关的服务已注册
                && $registrar->isConfigServicesBooted()
                // 进程相关的服务已注册
                && $registrar->isProcServicesBooted();

        // 激活过了直接继续.
        if ($activated) {
            return;
        }

        // 激活结束了.
        $this->console->info(
            $this->logInfo->bootingStartKeyStep(static::class . '::activate')
        );

        $this->console->debug($this->logInfo->bootingStartKeyStep('boot components'));
        $registrar->bootComponents($this);

        // 初始化所有的进程级服务.
        $this->console->debug($this->logInfo->bootingStartKeyStep('boot config service'));
        $registrar->bootConfigServices();

        $this->console->debug($this->logInfo->bootingStartKeyStep('boot proc service'));
        $registrar->bootProcServices();


        // 激活结束了.
        $this->console->info(
            $this->logInfo->bootingEndKeyStep(static::class . '::activate')
        );
        $this->activated = true;
    }

    public function bootstrap(): App
    {
        if ($this->ranBootstrap) {
            return $this;
        }

        try {
            // 总是要把 logo 打出来. 希望接下来不要出错打脸.
            $welcome = new Bootstrap\WelcomeToCommune();
            $welcome->bootstrap($this);

            // 正式初始化.
            $this->doBootstrap();

        } catch (\Throwable $e) {
            $this->console->emergency(strval($e));
            $this->fail();
        }

        $this->ranBootstrap = true;
        return $this;
    }

    protected function doBootstrap() : void
    {
        $this->console->info(
            $this->logInfo->bootingStartKeyStep(static::class . '::bootstrap')
        );

        foreach ($this->bootstrappers as $bootstrapper) {
            $this->console->debug(
                $this->logInfo->bootingStartBootstrapper($bootstrapper)
            );

            if (!is_a($bootstrapper, Bootstrapper::class, TRUE)) {
                throw new InvalidConfigException(
                    static::class . '::' . __FUNCTION__,
                    'bootstrapper',
                    'must be subclass of ' . Bootstrapper::class
                );
            }

            /**
             * @var Bootstrapper $bootstrapperIns
             */
            $bootstrapperIns = $this->procC->make($bootstrapper);
            $bootstrapperIns->bootstrap($this);

            $this->console->debug(
                $this->logInfo->bootingEndBootstrapper($bootstrapper)
            );
        }

        $this->console->info(
            $this->logInfo->bootingEndKeyStep(static::class . '::bootstrap')
        );
    }

    protected function fail(): void
    {
        $this->console->info('exit');
        if (isset($this->fail) && is_callable($this->fail)) {
            $caller = $this->fail;
            $caller();
        } else {
            exit(SIGTERM);
        }
    }

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }

}