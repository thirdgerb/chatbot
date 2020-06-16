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

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Framework\Handlers\AppProtocalHandler;
use Commune\Blueprint\Framework\ProcContainer;
use Commune\Blueprint\Framework\Request\AppProtocal;
use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Framework\Session;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Framework\Bootstrap;
use Commune\Blueprint\Exceptions\Logic\InvalidConfigException;
use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\Bootstrapper;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\ServiceRegistrar;
use Commune\Container\Container;
use Commune\Container\ContainerContract;
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\Log\LogInfo;
use Commune\Framework\Event\FinishRequest;
use Commune\Framework\Event\StartRequest;
use Commune\Framework\Log\IConsoleLogger;
use Commune\Framework\Log\ILogInfo;
use Commune\Framework\Spy\SpyAgency;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Protocal\Protocal;
use Commune\Support\Protocal\ProtocalMatcher;
use Commune\Support\Protocal\ProtocalOption;
use Commune\Support\Utils\ArrayUtils;
use Commune\Support\Utils\TypeUtils;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

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

    /**
     * @var ProtocalMatcher
     */
    protected $protocalMatcher;

    /**
     * @var callable|null
     */
    protected $fail;

    public function __construct(
        ContainerContract $procC = null,
        ReqContainer $reqC = null,
        ServiceRegistrar $registrar = null,
        ConsoleLogger $consoleLogger = null,
        LogInfo $logInfo = null
    )
    {
        $this->procC = $procC ?? new Container();

        $this->reqC = $reqC ?? new IReqContainer($this->procC);

        $startLevel = CommuneEnv::isDebug() ? LogLevel::DEBUG : LogLevel::INFO;
        $this->console = $consoleLogger ?? new IConsoleLogger(
            true,
                $startLevel
            );

        $this->logInfo = $logInfo ?? new ILogInfo();

        $this->registrar = $registrar ?? new IServiceRegistrar(
            $this->procC,
            $this->reqC,
            $this->console,
            $this->logInfo
        );

        // 不怕重复绑定.
        $this->instance(ProcContainer::class, $this->procC);
        $this->instance(ConsoleLogger::class, $this->console);
        $this->instance(LogInfo::class, $this->logInfo);
        $this->instance(ServiceRegistrar::class, $this->registrar);

        // 默认绑定关系.
        $this->basicBindings();

        SpyAgency::incr(static::class);
    }

    protected function instance($abstract, $instance) : void
    {
        $this->procC->instance($abstract, $instance);
        $this->reqC->instance($abstract, $instance);
    }

    /*------- abstract -------*/

    abstract protected function basicBindings() : void;

    /**
     * @return ProtocalOption[]
     */
    abstract protected function getProtocalOptions() : array;

    /**
     * @param ReqContainer $container
     * @param InputMsg $input
     * @return Session
     */
    abstract protected function makeInputSession(ReqContainer $container, InputMsg $input) : Session;

    /**
     * @param AppProtocal $protocal
     * @return bool
     */
    abstract protected function isValidFinaleResponse(AppProtocal $protocal) : bool;

    /*------ protocal ------*/

    public function getProtocalMatcher() : ProtocalMatcher
    {
        return $this->protocalMatcher
            ?? $this->protocalMatcher = new ProtocalMatcher(
                $this->getConsoleLogger(),
                $this->getProtocalOptions()
            );
    }

    public function eachProtocalHandler(
        ReqContainer $container,
        Protocal $protocal,
        string $handlerInterface = null
    ): \Generator
    {
        $matcher = $this->getProtocalMatcher();
        foreach ($matcher->matchEach($protocal, $handlerInterface) as $handlerOption) {
            $handler = $handlerOption->handler;
            $params = $handlerOption->params;
            yield $container->make($handler, $params);
        }
    }

    /*------ request ------*/

    public function handleRequest(AppRequest $request): AppResponse
    {
        $traceId = $request->getTraceId();
        /**
         * @var LoggerInterface $logger
         */
        $logger = $this->getProcContainer()->get(LoggerInterface::class);
        $requestStart = microtime(true);

        try {

            $error = $request->isInvalid();
            if (isset($error)) {
                $this->requestLog($logger, "badRequest: $error", $traceId);
                return $response = $request->fail(AppResponse::BAD_REQUEST);
            }

            // 根据请求衍生的唯一ID 来生成 container 的容器.
            $container = $this->newReqContainerIns($this->makeContainerId($request));

            // share
            $container->share(AppRequest::class, $request);
            $container->share(get_class($request), $request);

            // 创建 Session
            $input = $request->getInput();
            $session = $this->makeInputSession($container, $input);

            // 如果是无状态请求.
            if ($request->isStateless()) {
                $session->noState();
            }

            // boot 所有请求级服务.
            $this->getServiceRegistrar()->bootReqServices($container);

            // 抛出启动事件.
            $session->fire(new StartRequest($session));

            $response = $this->runAppProtocalHandlers($container, $request, $logger, $traceId);

        // 通用异常管理.
        } catch (\Throwable $e) {
            $this->report($e);
            $response = $request->fail(AppResponse::HOST_LOGIC_ERROR);

        // 垃圾回收与日志.
        } finally {

            // 垃圾回收
            if (isset($session)) {
                $session->fire(new FinishRequest($session));
                $session->finish();
            }

            // 记录请求时间.
            if (isset($logger)) {
                $requestEnd = microtime(true);
                $gap = round(($requestEnd - $requestStart) * 1000000, 0);
                $this->requestLog($logger, "finish request in $gap us", $traceId);
            }
            unset ($logger);

            // 默认 response.
            return $response
                ?? $request->fail(AppResponse::HOST_LOGIC_ERROR);
        }
    }

    protected function report(\Throwable $e) : void
    {
        /**
         * @var ExceptionReporter $expReporter
         */
        $expReporter = $this->getProcContainer()->get(ExceptionReporter::class);
        $expReporter->report($e);
        unset($expReporter);
    }

    protected function requestLog(
        LoggerInterface $logger,
        string $message,
        string $traceId,
        array $context = []
    ) : void
    {
        $context['trace'] = $traceId;
        $logger->info($message, $context);
    }

    protected function runAppProtocalHandlers(
        ReqContainer $container,
        AppRequest $request,
        LoggerInterface $logger,
        string $traceId
    ) : AppResponse
    {
        // 循环处理协议, 直到有正确结果为止.
        $protocal = $request;

        // 计数器
        $i = 0;
        $start = microtime(true);

        // 多次协议调度. 20 是一个不可能的值, 用于排查死循环. 未来可能改为配置.
        while ($i < 20) {

            if (! $protocal instanceof AppProtocal) {
                throw new CommuneLogicException(
                    'invalid running protocal that type is ' . TypeUtils::getType($protocal)
                );
            }

            // 获取处理协议的 handler
            $each = $this->eachProtocalHandler(
                $container,
                $protocal,
                AppProtocalHandler::class
            );

            /**
             * @var AppProtocalHandler $handler
             */
            $handler = ArrayUtils::first($each);
            unset($each);

            // Handler 不存在的情况
            if (!isset($handler)) {

                $logger->error(
                    __METHOD__
                    . ' handler not found for protocal '. TypeUtils::getType($protocal)
                );

                return $request->fail(AppResponse::HANDLER_NOT_FOUND);
            }

            // 记录日志准备.
            $protocalType = TypeUtils::getType($protocal);
            $handlerType = TypeUtils::getType($handler);


            // 使用 Handler 来响应.
            $protocal = $handler($protocal);
            unset($handler);

            // 记录 handler 日志, 方便排查问题.
            $gap = round((microtime(true) - $start) * 1000000, 0);
            $this->requestLog($logger, "AppProtocalHandler $handlerType for $protocalType done in {$gap}us", $traceId);

            // 检查是否为最终的合法 response.
            // 否则继续寻找可以处理的响应.
            if ($this->isValidFinaleResponse($protocal) && $protocal instanceof AppResponse) {
                return $protocal;
            }

            $i++ ;
        }

        throw new CommuneLogicException(
            "too many handler called, times $i"
        );
    }

    /**
     * 廉价地生成一个 container 的唯一ID
     * @param AppRequest $request
     * @return string
     */
    protected function makeContainerId(AppRequest $request) : string
    {
        return md5(
            $this->getId()
            . ':'
            . get_class($request)
            . ':'
            . $request->getTraceId()
        );
    }

    public function newReqContainerIns(string $uuid): ReqContainer
    {
        $container = $this->reqC->newInstance($uuid, $this->procC);
        $container->share(ReqContainer::class, $container);
        return $container;
    }

    /*------ getter ------*/


    public function getProcContainer(): ContainerContract
    {
        return $this->procC;
    }

    public function getBasicReqContainer(): ReqContainer
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

        $registrar = $this->getServiceRegistrar();

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
        if (isset($this->fail)) {
            $caller = $this->fail;
            $caller();
        } else {
            exit(1);
        }
    }

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }

}