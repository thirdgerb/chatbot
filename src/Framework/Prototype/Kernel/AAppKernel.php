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

use Commune\Framework\Blueprint\App;
use Commune\Framework\Blueprint\AppKernel;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Framework\Blueprint\Server\Request;
use Commune\Framework\Blueprint\Server\Response;
use Commune\Framework\Blueprint\Session\Session;
use Commune\Framework\Blueprint\Session\SessionPipe;
use Commune\Framework\Exceptions\ChatClientException;
use Commune\Framework\Exceptions\ChatRequestException;
use Commune\Framework\Exceptions\ChatSessionException;
use Commune\Framework\Prototype\Session\Events\FinishSession;
use Commune\Framework\Prototype\Session\Events\StartSession;
use Commune\Support\Pipeline\OnionPipeline;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AAppKernel implements AppKernel
{
    /**
     * @var App
     */
    protected $app;

    /**
     * AKernel constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    abstract public function basicReqBinding(ReqContainer $container) : void;

    public function onRequest(
        Request $request,
        Response $response,
        array $middleware,
        string $via
    ): void
    {
        $chatId = $request->getChatId();

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

            $session->fire(new StartSession());
            $this->sendSessionThroughPipes($session, $via, $middleware);

            // 完成响应.
            $response->sendResponse();

        // 请求偶发的异常
        // 不记录日志. 日志通常应该抛异常的地方记录.
        } catch (ChatRequestException $e) {
            $response->sendFailureResponse($e);

        // 客户端连接需要重置
        } catch (ChatClientException $e) {
            $this->app->getExceptionReporter()->report($e);
            $this->app->getServer()->closeClient($chatId);

        // session 级别的异常.
        } catch (ChatSessionException $e) {
            $this->app->getExceptionReporter()->report($e);
            // 重置当前 session.
            if (isset($session)) {
                $session->reset();
            }

        // 未预料到的错误.
        } catch (\Throwable $e) {
            $this->app->getExceptionReporter()->report($e);
            // 发送默认的异常信息.
            $response->sendFailureResponse();

        } finally {

            // 关闭 Session, 关闭客户端
            if (isset($session)) {
                $session->fire(new FinishSession());
                $session->finish();
            }

            if (isset($reqContainer)) {
                $reqContainer->finish();
            }
        }
    }

    public function handleRequest(
        Request $request,
        Response $response,
        array $middleware
    ): void
    {
        $this->onRequest(
            $request,
            $response,
            $middleware,
            SessionPipe::SYNC
        );
    }

    public function asyncHandleRequest(
        Request $request,
        Response $response,
        array $middleware
    ): void
    {
        $this->onRequest(
            $request,
            $response,
            $middleware,
            SessionPipe::ASYNC_INPUT
        );
    }

    public function asyncHandleResponse(
        Request $request,
        Response $response,
        array $middleware
    ): void
    {
        $this->onRequest(
            $request,
            $response,
            $middleware,
            SessionPipe::ASYNC_OUTPUT
        );
    }


    /**
     * 通过管道来运行 Session
     *
     * @param Session $session
     * @param string $via
     * @param array $middleware
     * @return Session
     */
    protected function sendSessionThroughPipes(
        Session $session,
        string $via,
        array $middleware
    ) : Session
    {
        $pipeline = new OnionPipeline($session->getContainer());

        foreach ($middleware as $pipe) {
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
        if (!$request->validate()) {
            return false;
        }

        $warning = $this->app
            ->getLogInfo()
            ->appReceiveInvalidRequest($request->getBrief());

        $this->app
            ->getLogger()
            ->warning($warning);

        return true;
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
            ->newInstance($request->getUuid(), $procContainer);

        // 绑定 request
        $reqContainer->share(ReqContainer::class, $reqContainer);

        $reqContainer->share(Request::class, $request);
        $reqContainer->share(Response::class, $response);

        $this->basicReqBinding($reqContainer);

        // 重新 boot 服务.
        $this->app->bootReqServices($reqContainer);
        return $reqContainer;
    }

}