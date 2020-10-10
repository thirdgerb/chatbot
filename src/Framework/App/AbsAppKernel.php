<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\App;

use Commune\Framework\AbsApp;
use Commune\Blueprint\Kernel\AppKernel;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Kernel\Protocols\AppRequest;
use Commune\Blueprint\Kernel\Protocols\AppResponse;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Framework\Event\FinishRequest;
use Commune\Framework\Event\StartRequest;
use Commune\Support\Utils\ArrayUtils;
use Commune\Support\Utils\TypeUtils;
use Illuminate\Contracts\Container\BindingResolutionException;
use Psr\Log\LoggerInterface;
use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Support\Protocol\Protocol;
use Commune\Support\Protocol\ProtocolMatcher;
use Commune\Support\Protocol\ProtocolOption;
use Commune\Blueprint\Exceptions\CommuneRuntimeException;



/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
abstract class AbsAppKernel extends AbsApp implements AppKernel
{
    /**
     * 对 Protocol 进行通配时, 只匹配 . 与 \w.
     * 更复杂的匹配规则请自行定义, 或者定义到 Protocol handler interface 内部.
     *
     * @var string
     */
    protected $wildcardPattern = '[\.\w]+';

    /**
     * @var ProtocolMatcher
     */
    protected $ProtocolMatcher;

    /**
     * @return ProtocolOption[]
     */
    abstract protected function getProtocolOptions() : array;


    /**
     * @param ReqContainer $container
     * @param AppRequest $request
     * @return Session
     */
    abstract protected function makeSession(ReqContainer $container, AppRequest $request) : Session;

    abstract protected function failResponse(
        string $traceId,
        int $errcode = AppResponse::HOST_REQUEST_FAIL,
        string $errmsg = ''
    ) : AppResponse;

    /**
     * @param AppRequest $request
     * @throws CommuneRuntimeException
     */
    abstract protected function validateAppRequest(AppRequest $request) : void;

    /*------ Protocol ------*/

    public function getProtocolMatcher() : ProtocolMatcher
    {
        return $this->ProtocolMatcher
            ?? $this->ProtocolMatcher = new ProtocolMatcher(
                $this->getConsoleLogger(),
                $this->getProtocolOptions(),
                $this->wildcardPattern
            );
    }

    public function eachProtocolHandler(
        ReqContainer $container,
        Protocol $Protocol,
        string $handlerInterface = null
    ): \Generator
    {
        $matcher = $this->getProtocolMatcher();

        foreach ($matcher->matchEach($Protocol, $handlerInterface) as $handlerOption) {
            $handlerName = $handlerOption->handler;
            $params = $handlerOption->params;

            $handler = $container->make($handlerName, $params);

            if (isset($handlerInterface) && !is_a($handler, $handlerInterface, TRUE)) {
                $actual = TypeUtils::getType($handler);
                throw new CommuneLogicException(
                    "invalid Protocol handler, expect $handlerInterface, $actual given"
                );
            }

            yield $handler;
        }
    }

    public function firstProtocolHandler(
        ReqContainer $container,
        Protocol $Protocol,
        string $handlerInterface = null
    ): ? callable
    {
        $each = $this->eachProtocolHandler(
            $container,
            $Protocol,
            $handlerInterface
        );

        $caller = ArrayUtils::first($each);
        return is_callable($caller) ? $caller : null;
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

    /*------ request ------*/


    /**
     * @param AppRequest $request
     * @param string|null $interface
     * @param ReqContainer|null $container
     * @return AppResponse
     */
    public function handleRequest(
        AppRequest $request,
        string $interface = null,
        ReqContainer $container = null
    ) : AppResponse
    {

        $traceId = $request->getTraceId();
        /**
         * @var LoggerInterface $logger
         */
        $logger = $this->getProcContainer()->get(LoggerInterface::class);
        $requestStart = microtime(true);

        try {
            $this->validateAppRequest($request);

            // 根据请求衍生的唯一ID 来生成 container 的容器.
            $container = $container
                    ?? $this->newReqContainerIns($this->makeContainerId($request));

            // 创建 Session
            $session = $this->makeSession($container, $request);

            // boot 所有请求级服务.
            $this->getServiceRegistry()->bootReqServices($container);

            // 寻找 handler
            $handler = $this->firstProtocolHandler(
                $container,
                $request,
                $interface
            );

            if (!isset($handler)) {
                throw new CommuneLogicException(
                    "request handler not found for " . TypeUtils::getType($request)
                    . ", interface $interface, on "
                    . static::class
                );
            }

            // 抛出启动事件.
            $session->fire(new StartRequest($session));

            $response = $handler($request);

        // 容器绑定关系的问题.
        } catch (BindingResolutionException $e) {
            $this->report($e);
            $response = $this->failResponse(
                $traceId,
                $code = AppResponse::HOST_LOGIC_ERROR,
                AppResponse::DEFAULT_ERROR_MESSAGES[$code]
            );

        } catch (CommuneRuntimeException $e) {
            $this->report($e);
            $response = $this->failResponse($traceId, $e->getCode(), $e->getMessage());

        } catch (CommuneLogicException $e) {
            $this->report($e);
            $response = $this->failResponse($traceId, $e->getCode(), $e->getMessage());

        // 通用异常管理.
        } catch (\Throwable $e) {
            $this->report($e);
            $response = $this->failResponse($traceId);

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
                $usage = memory_get_peak_usage();
                $this->requestLog($logger, static::class . " finish request in $gap us, memory usage $usage", $traceId);
            }
            unset ($logger);

            // 默认 response.
            return $response ?? $this->failResponse($traceId);
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
        $context['appId'] = $this->getId();

        $logger->info($message, $context);
    }


}