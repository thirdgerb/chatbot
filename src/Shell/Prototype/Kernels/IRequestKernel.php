<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\Prototype\Kernels;

use Commune\Framework\Blueprint\ReqContainer;
use Commune\Shell\Blueprint\Pipeline\ShellPipe;
use Commune\Shell\Blueprint\Session\ShlSession;
use Commune\Shell\Blueprint\Shell;
use Commune\Shell\Blueprint\Kernels\RequestKernel;
use Commune\Shell\Contracts\ShlRequest;
use Commune\Shell\Contracts\ShlResponse;
use Commune\Shell\Exceptions\RequestException;
use Commune\Shell\Prototype\Events\FinishShlSession;
use Commune\Shell\Prototype\Events\StartShlSession;
use Commune\Shell\ShellConfig;
use Commune\Support\Pipeline\OnionPipeline;


/**
 * Shell 的请求处理内核.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRequestKernel implements RequestKernel
{
    /**
     * @var Shell
     */
    protected $shell;

    /**
     * @var ShellConfig
     */
    protected $shellConfig;


    /**
     * IRequestKernel constructor.
     * @param Shell $shell
     * @param ShellConfig $shellConfig
     */
    public function __construct(Shell $shell, ShellConfig $shellConfig)
    {
        $this->shell = $shell;
        $this->shellConfig = $shellConfig;
    }


    public function onRequest(
        ShlRequest $request,
        ShlResponse $response
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
             * @var ShlSession $session
             */
            $session = $reqContainer->get(ShlSession::class);
            $session->fire(new StartShlSession());

            $session = $this->sendSessionThroughPipes($session);

            // 完成响应.
            $response->sendResponse();

        // 请求本身可发回的异常
        // 不记录日志. 日志通常应该抛异常的地方记录.
        } catch (RequestException $e) {
            $response->sendFailureResponse($e);

        // 关闭 Session, 关闭客户端


        // 未预料到的错误.
        } catch (\Throwable $e) {

            $this->shell->getExceptionReporter()->report($e);
            // 发送默认的异常信息.
            $response->sendFailureResponse();

        } finally {

            if (isset($session)) {
                $session->fire(new FinishShlSession());
                $session->finish();
            }

            if (isset($reqContainer)) {
                $reqContainer->finish();
            }
        }
    }

    protected function validateRequest(ShlRequest $request) : bool
    {
        if ($request->validate()) {
            return true;
        }

        $warning = $this->shell
            ->getLogInfo()
            ->shellReceiveInvalidRequest($request->getBrief());

        $this->shell
            ->getLogger()
            ->warning($warning);

        return false;
    }

    protected function createReqContainer(
        ShlRequest $request,
        ShlResponse $response
    ) : ReqContainer
    {
        $procContainer = $this->shell->getProcContainer();

        // 获取新的请求级实例.
        $reqContainer =  $this->shell
            ->getReqContainer()
            ->newInstance($request->getTraceId(), $procContainer);

        // 绑定 request
        $reqContainer->share(ReqContainer::class, $reqContainer);
        $reqContainer->share(ShlRequest::class, $request);
        $reqContainer->share(ShlResponse::class, $response);

        // 重新 boot 服务.
        $this->shell->bootReqServices($reqContainer);
        return $reqContainer;
    }


    /**
     * 通过管道来运行 Session
     *
     * @param ShlSession $session
     * @return ShlSession
     */
    protected function sendSessionThroughPipes(ShlSession $session) : ShlSession
    {
        $pipes = $this->shellConfig->pipeline;
        $pipeline = new OnionPipeline($session->container);

        while($pipe = array_shift($pipes)) {
            $pipeline->through($pipe);
        }

        $pipeline->via(ShellPipe::HANDLER);

        // 发送会话
        /**
         * @var ShlSession $session
         */
        $session = $pipeline->send(
            $session,
            function (ShlSession $session): ShlSession {
                return $session;
            }
        );

        return $session;
    }


}