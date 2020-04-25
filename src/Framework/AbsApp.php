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

use Commune\Blueprint\Framework\App;
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
        // 默认绑定关系.
        $this->basicBindings();
    }

    abstract protected function basicBindings() : void;

    public function isDebugging(): bool
    {
        return $this->debug;
    }

    public function newReqContainerInstance(string $uuid): ReqContainer
    {
        return $this->reqC->newInstance($uuid, $this->procC);
    }

    public function getProcContainer(): ContainerContract
    {
        return $this->procC;
    }

    public function getReqContainer(): ContainerContract
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


}