<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Kernels;

use Commune\Framework\Blueprint\Intercom\GhostInput;
use Commune\Framework\Blueprint\ReqContainer;
use Commune\Ghost\Blueprint\Ghost;
use Commune\Ghost\Blueprint\Pipeline\GhostPipe;
use Commune\Ghost\Blueprint\Session\GhtSession;
use Commune\Ghost\Contracts\GhtRequest;
use Commune\Ghost\Contracts\GhtResponse;
use Commune\Ghost\Prototype\Events\FinishGhostSession;
use Commune\Ghost\Prototype\Events\StartGhostSession;
use Commune\Framework\Exceptions\RequestException;
use Commune\Support\Pipeline\OnionPipeline;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AKernel
{
    /*------- 配置 ------*/

    protected $startPipeline = [];

    protected $endPipeline = [];

    /*------- cached ------*/

    /**
     * @var Ghost
     */
    protected $ghost;

    /**
     * AKernel constructor.
     * @param Ghost $ghost
     */
    public function __construct(Ghost $ghost)
    {
        $this->ghost = $ghost;
    }


    /**
     * @return string[]
     */
    abstract public function getUserMiddleware() : array;

    public function onRequest(
        GhtRequest $request,
        GhtResponse $response
    ): void
    {
        try {

            // 请求不合法
            if (!$this->validateRequest($request)) {
                $response->sendRejectResponse();
                return;
            }

            $this->onInput($request->getInput());

            // 完成响应.
            $response->sendResponse();

            // 请求本身可发回的异常
            // 不记录日志. 日志通常应该抛异常的地方记录.
        } catch (RequestException $e) {
            $response->sendFailureResponse($e);
            // 关闭 Session, 关闭客户端

        // 未预料到的错误.
        } catch (\Throwable $e) {
            $this->ghost->getExceptionReporter()->report($e);
            // 发送默认的异常信息.
            $response->sendFailureResponse();
        }
    }

    public function onInput(
        GhostInput $input
    ) : void
    {
        try {

            $reqContainer = $this->createReqContainer($input);
            /**
             * @var GhtSession $session
             */
            $session = $reqContainer->get(GhtSession::class);
            $session->fire(new StartGhostSession());
            $session = $this->sendSessionThroughPipes($session);

        } catch (\Throwable $e) {
            $this->ghost->getExceptionReporter()->report($e);

        } finally {

            if (isset($session)) {
                $session->fire(new FinishGhostSession());
                $session->finish();
            }

            if (isset($reqContainer)) {
                $reqContainer->finish();
            }
        }

    }

    protected function createReqContainer(
        GhostInput $input
    ) : ReqContainer
    {
        $procContainer = $this->ghost->getProcContainer();

        // 获取新的请求级实例.
        $reqContainer =  $this->ghost
            ->getReqContainer()
            ->newInstance($input->messageId, $procContainer);

        // 绑定 request
        $reqContainer->share(ReqContainer::class, $reqContainer);
        $reqContainer->share(GhostInput::class, $input);

        // 重新 boot 服务.
        $this->ghost->bootReqServices($reqContainer);
        return $reqContainer;
    }

    /**
     * 通过管道来运行 Session
     *
     * @param GhtSession $session
     * @return GhtSession
     */
    protected function sendSessionThroughPipes(GhtSession $session) : GhtSession
    {
        $pipeline = new OnionPipeline($session->container);

        // 合成为管道.
        $pipes = array_merge(
            $this->startPipeline,
            $this->endPipeline
        );

        foreach ($pipes as $pipe) {
            $pipeline->through($pipe);
        }

        $pipeline->via(GhostPipe::HANDLER);

        // 发送会话
        /**
         * @var GhtSession $session
         */
        $session = $pipeline->send(
            $session,
            function (GhtSession $session): GhtSession {
                return $session;
            }
        );

        return $session;
    }


    protected function validateRequest(GhtRequest $request) : bool
    {
        if ($request->validate()) {
            return true;
        }

        $warning = $this->ghost
            ->getLogInfo()
            ->ghostReceiveInvalidRequest($request->getBrief());

        $this->ghost
            ->getLogger()
            ->warning($warning);

        return false;
    }
}