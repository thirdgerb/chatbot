<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Kernel;

use Commune\Framework\Blueprint\ChatApp;
use Commune\Framework\Blueprint\Kernel\Kernel;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Blueprint\Server\Request;
use Commune\Framework\Blueprint\Server\Response;
use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Blueprint\Session\SessionPipe;
use Commune\Framework\Exceptions\RequestException;
use Commune\Framework\Prototype\Session\Events\FinishSession;
use Commune\Framework\Prototype\Session\Events\StartSession;
use Commune\Support\Pipeline\OnionPipeline;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AKernel implements Kernel
{
    /**
     * @var ChatApp
     */
    protected $app;

    /**
     * AKernel constructor.
     * @param ChatApp $app
     */
    public function __construct(ChatApp $app)
    {
        $this->app = $app;
    }

    abstract public function getPipes() : array;

    public function onRequest(
        Request $request,
        Response $response,
        string $via,
        bool $noState = false
    ): void
    {
        try {

            // 请求不合法
            if (!$this->validateRequest($request)) {
                $response->sendRejectResponse();
                return;
            }

            $reqContainer = $this->createReqContainer($request, $response);

            /**
             * @var Session $session
             */
            $session = $reqContainer->make(Session::class);
            if ($noState) {
                $session->noState();
            }

            $session->fire(new StartSession());
            $this->sendSessionThroughPipes($session, $via);

            // 完成响应.
            $response->sendResponse();

            // 请求本身可发回的异常
            // 不记录日志. 日志通常应该抛异常的地方记录.
        } catch (RequestException $e) {
            $response->sendFailureResponse($e);
            // 关闭 Session, 关闭客户端

            // 未预料到的错误.
        } catch (\Throwable $e) {
            $this->app->getExceptionReporter()->report($e);
            // 发送默认的异常信息.
            $response->sendFailureResponse();

        } finally {

            if (isset($session)) {
                $session->fire(new FinishSession());
                $session->finish();
            }

            if (isset($reqContainer)) {
                $reqContainer->finish();
            }
        }
    }

    public function onSyncRequest(
        Request $request,
        Response $response,
        bool $noState = false
    ): void
    {
        $this->onRequest($request, $response, SessionPipe::SYNC, $noState);
    }

    public function onAsyncRequest(
        Request $request,
        Response $response,
        bool $noState = false
    ): void
    {
        $this->onRequest($request, $response, SessionPipe::ASYNC_INPUT, $noState);
    }

    public function onAsyncResponse(
        Request $request,
        Response $response,
        bool $noState = false
    ): void
    {
        $this->onRequest($request, $response, SessionPipe::ASYNC_OUTPUT, $noState);
    }


    /**
     * 通过管道来运行 Session
     *
     * @param Session $session
     * @param string $via
     * @return Session
     */
    protected function sendSessionThroughPipes(Session $session, string $via) : Session
    {
        $pipeline = new OnionPipeline($session->getContainer());

        // 合成为管道.
        $pipes = $this->getPipes();

        foreach ($pipes as $pipe) {
            $pipeline->through($pipe);
        }

        $pipeline->via($via);

        // 发送会话
        /**
         * @var Session $session
         */
        $session = $pipeline->send(
            $session,
            function (Session $session): Session {
                return $session;
            }
        );

        return $session;
    }



    protected function validateRequest(Request $request) : bool
    {
        if ($request->validate()) {
            return true;
        }

        $warning = $this->app
            ->getLogInfo()
            ->appReceiveInvalidRequest($request->getBrief());

        $this->app
            ->getLogger()
            ->warning($warning);

        return false;
    }


    protected function createReqContainer(
        Request $request,
        Response $response
    ) : ReqContainer
    {
        $procContainer = $this->app->getProcContainer();

        // 获取新的请求级实例.
        $reqContainer =  $this->app
            ->getReqContainer()
            ->newInstance($request->getTraceId(), $procContainer);

        // 绑定 request
        $reqContainer->share(ReqContainer::class, $reqContainer);

        foreach ($request->getInterfaces() as $interface) {
            $reqContainer->share($interface, $request);
        }

        foreach ($response->getInterfaces() as $interface) {
            $reqContainer->share($interface, $response);
        }

        // 重新 boot 服务.
        $this->app->bootReqServices($reqContainer);
        return $reqContainer;
    }
}