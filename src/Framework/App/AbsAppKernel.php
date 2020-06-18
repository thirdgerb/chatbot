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
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Framework\Event\FinishRequest;
use Commune\Framework\Event\StartRequest;
use Commune\Support\Utils\TypeUtils;
use Psr\Log\LoggerInterface;
use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Support\Protocal\Protocal;
use Commune\Support\Protocal\ProtocalMatcher;
use Commune\Support\Protocal\ProtocalOption;



/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
abstract class AbsAppKernel extends AbsApp implements AppKernel
{



    /**
     * @var ProtocalMatcher
     */
    protected $protocalMatcher;

    /**
     * @return ProtocalOption[]
     */
    abstract protected function getProtocalOptions() : array;


    /**
     * @param ReqContainer $container
     * @param AppRequest $request
     * @return Session
     */
    abstract protected function makeSession(ReqContainer $container, AppRequest $request) : Session;


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
     * @return AppResponse
     */
    public function handleRequest(
        AppRequest $request,
        string $interface = null
    ) : AppResponse
    {
        $traceId = $request->getTraceId();

        /**
         * @var LoggerInterface $logger
         */
        $logger = $this->getProcContainer()->get(LoggerInterface::class);
        $requestStart = microtime(true);

        try {

            $failedResponse = $request->validate();

            if (isset($failedResponse)) {
                $error = $failedResponse->getErrmsg();
                $this->requestLog($logger, "badRequest: $error", $traceId);
                return $failedResponse;
            }

            // 根据请求衍生的唯一ID 来生成 container 的容器.
            $container = $this->newReqContainerIns($this->makeContainerId($request));

            // share
            $container->share(AppRequest::class, $request);
            $container->share(get_class($request), $request);

            // 创建 Session
            $session = $this->makeSession($container, $request);

            // 如果是无状态请求.
            if ($request->isStateless()) {
                $session->noState();
            }

            // boot 所有请求级服务.
            $this->getServiceRegistry()->bootReqServices($container);

            // 寻找 handler
            $handler = $this->firstProtocalHandler(
                $container,
                $request,
                $interface
            );

            if (!isset($handler)) {
                throw new CommuneLogicException(
                    "request handler not found for " . TypeUtils::getType($request)
                );
            }


            // 抛出启动事件.
            $session->fire(new StartRequest($session));

            $response = $handler($request);

            // 通用异常管理.
        } catch (\Throwable $e) {
            $this->report($e);
            $response = $request->response(AppResponse::HOST_LOGIC_ERROR);

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
                ?? $request->response(AppResponse::HOST_LOGIC_ERROR);
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


}