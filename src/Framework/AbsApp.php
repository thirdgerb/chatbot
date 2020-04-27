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

use Commune\Blueprint\Exceptions\HostBootingException;
use Commune\Blueprint\Exceptions\Logic\InvalidConfigException;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\Bootstrapper;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\ServiceRegistrar;
use Commune\Container\Container;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\LogInfo;
use Commune\Framework\Log\IConsoleLogger;
use Commune\Framework\Log\ILogInfo;
use Commune\Framework\Prototype\IReqContainer;

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
     * @var bool
     */
    protected $debug;

    /**
     * @var ContainerContract
     */
    protected $procC;

    /**
     * @var ReqContainer
     */
    protected $reqC;

    /**
     * @var ServiceRegistrar
     */
    protected $registrar;

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


    public function __construct(
        bool $debug,
        ? ContainerContract $procC,
        ? ReqContainer $reqC,
        ? ServiceRegistrar $registrar,
        ? ConsoleLogger $consoleLogger,
        ? LogInfo $logInfo
    )
    {
        $this->debug = $debug;
        $this->procC = $procC ?? new Container();
        $this->reqC = $reqC ?? new IReqContainer($this->procC);
        $this->console = $consoleLogger ?? new IConsoleLogger($debug);
        $this->logInfo = $logInfo ?? new ILogInfo();
        $this->registrar = $registrar ?? new IServiceRegistrar(
            $this->procC,
            $this->reqC,
            $this->console,
            $this->logInfo
        );

        // 不怕重复绑定.
        $this->instance(ConsoleLogger::class, $this->console);
        $this->instance(LogInfo::class, $this->logInfo);
        $this->instance(ServiceRegistrar::class, $this->registrar);

        // 默认绑定关系.
        $this->basicBindings();
    }

    abstract protected function basicBindings() : void;

    public function isDebugging(): bool
    {
        return $this->debug;
    }

    protected function instance($abstract, $instance) : void
    {
        $this->procC->instance($abstract, $instance);
        $this->reqC->instance($abstract, $instance);
    }

    public function newReqContainerInstance(string $uuid): ReqContainer
    {
        $container = $this->reqC->newInstance($uuid, $this->procC);
        $container->share(ReqContainer::class, $container);
        return $container;
    }

    public function getProcContainer(): ContainerContract
    {
        return $this->procC;
    }

    public function getReqContainer(): ReqContainer
    {
        return $this->reqC;
    }

    public function getServiceRegistrar(): ServiceRegistrar
    {
        return $this->registrar;
    }

    public function getConsoleLogger(): ConsoleLogger
    {
        return $this->console;
    }

    public function getLogInfo(): LogInfo
    {
        return $this->logInfo;
    }

    /**
     * 项目总启动.
     * @return App
     */
    public function activate(): App
    {
        if (!$this->ranBootstrap) {
            throw new HostBootingException(
                "app should run bootstrap() before activate()"
            );
        }

        $registrar = $this->getServiceRegistrar();

        // 检查是否已经激活过了.
        $activated = $this->activated
            ?? $this->activated = $registrar->isComponentsBooted()
                && $registrar->isConfigServicesBooted()
                && $registrar->isProcServicesBooted();

        // 激活过了直接继续.
        if ($activated) {
            return $this;
        }

        $this->console->notice(
            $this->logInfo->bootStartKeyStep(__METHOD__)
        );

        // 第一步, 初始化所有的组件.
        $registrar->bootComponents();

        // 第二步, 初始化所有的进程级服务.
        $registrar->bootConfigServices();
        $registrar->bootProcServices();

        // 激活结束了.
        $this->console->info(
            $this->logInfo->bootEndKeyStep(__METHOD__)
        );

        return $this;
    }

    public function bootstrap(): App
    {
        if ($this->ranBootstrap) {
            return $this;
        }

        $this->console->notice(
            $this->logInfo->bootStartKeyStep(__METHOD__)
        );

        foreach ($this->bootstrappers as $bootstrapper) {
            $this->console->debug(
                $this->logInfo->bootStartKeyStep($bootstrapper)
            );

            if (!is_a($bootstrapper, Bootstrapper::class, TRUE)) {
                throw new InvalidConfigException(
                    __METHOD__,
                    'bootstrapper',
                    'must be subclass of ' . Bootstrapper::class
                );
            }

            /**
             * @var Bootstrapper $bootstrapperIns
             */
            $bootstrapperIns = new $bootstrapper($this);
            $bootstrapperIns->bootstrap();
        }


        $this->console->info(
            $this->logInfo->bootEndKeyStep(__METHOD__)
        );
        $this->ranBootstrap = true;
        return $this;
    }


}