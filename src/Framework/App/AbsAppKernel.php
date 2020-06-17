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

use Commune\Blueprint\Exceptions\Runtime\BrokenConversationException;
use Commune\Blueprint\Framework\AppKernel;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Framework\Session;
use Commune\Blueprint\Kernel\Handlers\AppProtocalHandler;
use Commune\Blueprint\Kernel\Protocals\AppProtocal;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Framework\AbsApp;
use Commune\Framework\Event\FinishRequest;
use Commune\Framework\Event\StartRequest;
use Commune\Protocals\Intercom\InputMsg;
use Commune\Support\Utils\ArrayUtils;
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
     * @param ReqContainer $container
     * @param InputMsg $input
     * @return Session
     */
    abstract protected function makeInputSession(ReqContainer $container, InputMsg $input) : Session;



    /**
     * @var ProtocalMatcher
     */
    protected $protocalMatcher;

    /**
     * @return ProtocalOption[]
     */
    abstract protected function getProtocalOptions() : array;

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
     * @param string|null $expect
     * @param int $turns
     * @return AppResponse
     */
    public function handleRequest(
        AppRequest $request,
        string $expect,
        int $turns = 0
    ) : AppResponse
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
            $this->getServiceRegistry()->bootReqServices($container);

            // 抛出启动事件.
            $session->fire(new StartRequest($session));

            $response = $this->runAppProtocalHandlers(
                $container,
                $request,
                $logger,
                $traceId,
                $expect,
                $turns
            );

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
        AppProtocal $request,
        LoggerInterface $logger,
        string $traceId,
        string $expect,
        int $turns = 0
    ) : AppProtocal
    {
        // 循环处理协议, 直到有正确结果为止.
        $protocal = $request;

        // 计数器
        $i = 0;
        $turns = $turns > 10 ? 10 : $turns;

        // 准备计时
        $start = microtime(true);

        // 多次协议调度. 20 是一个不可能的值, 用于排查死循环. 未来可能改为配置.
        while ($turns > 0 && $i < $turns) {

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

            $exists = isset($handler);
            // Handler 不存在的情况
            // 无法处理, 又没有期待时, 直接报错.
            if (!$exists) {
                throw new BrokenConversationException(
                    ' handler not found for protocal '. TypeUtils::getType($protocal)
                );
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
            // 否则继续运行寻找可以处理的响应.
            if (is_a($protocal, $expect, true)) {
                return $protocal;
            }

            $i++ ;
        }

        throw new CommuneLogicException(
            "too many handler called, expect $expect, times $i, max $turns"
        );
    }




}