<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Kernel\Handlers;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Kernel\Handlers\GhostInputReceiver;
use Commune\Blueprint\Kernel\Protocals\AppProtocal;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\CloneRequest;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Contracts\Messenger\Messenger;
use Commune\Framework\Event\FinishRequest;
use Commune\Ghost\Kernel\Protocals\ICloneRequest;
use Commune\Ghost\Support\ValidateUtils;
use Commune\Message\Host\SystemInt\SessionBusyInt;
use Commune\Protocals\HostMsg\Convo\UnsupportedMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IGhostInputReceiver implements GhostInputReceiver
{
    /**
     * @var Cloner
     */
    protected $clone;

    /**
     * IGhostInputReceiver constructor.
     * @param Cloner $clone
     */
    public function __construct(Cloner $clone)
    {
        $this->clone = $clone;
    }

    /**
     * @param GhostRequest $protocal
     * @return CloneRequest|GhostResponse
     */
    public function __invoke($protocal)
    {
        // 类型校验.
        ValidateUtils::isArgInstanceOf($protocal, GhostRequest::class, true);

        $error = $protocal->isInvalid();
        if (isset($error)) {
            $this->clone->logger->warning("bad request $error");
            return $protocal->fail(AppResponse::BAD_REQUEST);
        }

        // 无状态设定.
        if ($protocal->isStateless()) {
            $this->clone->noState();
        }

        $input = $protocal->getInput();


        // 如果是不支持的消息, 则无响应.
        $message = $input->getMessage();
        if ($message instanceof UnsupportedMsg) {
            $this->clone->noState();
            return $protocal->fail(AppResponse::NO_CONTENT);
        }

        // 无状态请求, 无锁前进.
        if ($this->clone->isStateless()) {
            return $this->wrapCloneRequest($protocal);
        }

        // 设置好 shell session ID, 方便广播时处理.
        $this->setShellSessionId($protocal);

        $ttl = $this->clone->config->sessionLockerExpire;
        // 锁 clone
        if ($this->clone->lock($ttl)) {
            // 注册解锁逻辑.
            $this->clone->listen(FinishRequest::class, function(Cloner $clone){
                $clone->unlock();
            });

            // 成功则都往后走.
            return $this->wrapCloneRequest($protocal);
        }

        // 异步请求和同步请求最大的区别在于锁失败后的处理逻辑
        return $protocal->isAsync()
            ? $this->asyncLockFail($protocal)
            : $this->lockFail($protocal);
    }

    /**
     * 设置好路由关系.
     * @param GhostRequest $request
     */
    protected function setShellSessionId(GhostRequest $request) : void
    {
        $storage = $this->clone->storage;
        $routes = $storage->shellSessionRoutes ?? [];
        $shellName = $request->getInput()->getShellName();
        $routes[$shellName] = $request->getShellSessionId();
        $storage->shellSessionRoutes = $routes;
    }

    /**
     * 异步消息锁失败.
     * @param GhostRequest $request
     * @return GhostResponse
     */
    protected function asyncLockFail(GhostRequest $request) : GhostResponse
    {
        /**
         * @var Messenger $messenger
         */
        $messenger = $this->clone->container->get(Messenger::class);
        $messenger->asyncSendInput2Ghost($request->getInput());

        return $request->noContent();
    }

    /**
     * 同步消息锁失败.
     *
     * @param GhostRequest $request
     * @return GhostResponse|CloneRequest
     */
    protected function lockFail(GhostRequest $request) : AppProtocal
    {
        $this->clone->noState();
        return $request->output(new SessionBusyInt());
    }


    protected function wrapCloneRequest(GhostRequest $request) : CloneRequest
    {
        return new ICloneRequest(
            $request->getInput(),
            $request->isAsync(),
            $request->requireTinyResponse(),
            $request->isStateless()
        );
    }

}
