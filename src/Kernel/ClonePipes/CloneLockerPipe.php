<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\ClonePipes;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Contracts\Messenger\GhostMessenger;
use Commune\Contracts\Messenger\ShellMessenger;
use Commune\Framework\Event\FinishRequest;
use Commune\Message\Host\SystemInt\SessionBusyInt;
use Swoole\Coroutine;

/**
 * 请求锁的管道. 防止高并发裂脑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneLockerPipe extends AClonePipe
{
    /**
     * @param GhostRequest $request
     * @param \Closure $next
     * @return GhostResponse
     * @throws \Exception
     */
    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        if ($this->cloner->isStateless()) {
            $this->cloner->noState();
            return $next($request);
        }

        $isAsync = $request->isAsync();

        $ttl = $this->cloner->config->sessionLockerExpire;

        // 锁 clone
        if ($this->lock($isAsync, $ttl)) {
            // 注册解锁逻辑.
            $this->cloner->listen(
                FinishRequest::class,
                function(Cloner $cloner){
                    $cloner->unlock();
                }
            );

            return $next($request);
        }

        $this->cloner->noState();
        // 异步请求和同步请求最大的区别在于锁失败后的处理逻辑
        return $isAsync
            ? $this->asyncLockFail($request)
            : $this->lockFail($request);
    }

    protected function lock(bool $async, int $ttl) : bool
    {
        // 同步直接锁.
        if (!$async) {
            return $this->cloner->lock($ttl);
        }

        // 如果是在非协程环境下, 也直接锁.
        if (class_exists(Coroutine::class) && Coroutine::getCid() > 0) {
            return $this->cloner->lock($ttl);
        }

        // 循环两次, 用协程的方式挂起.
        for ( $i = 0; $i < 2 ; $i ++) {

            // 锁好了直接返回.
            if ($this->cloner->lock($ttl)) {
                return true;
            }

            // 协程挂起.
            Coroutine::sleep(1);
        }

        // 最后一次直接返回.
        return $this->cloner->lock($ttl);
    }

    /**
     * 异步消息锁失败.
     * @param GhostRequest $request
     * @return GhostResponse
     */
    protected function asyncLockFail(GhostRequest $request) : GhostResponse
    {
        /**
         * @var GhostMessenger $messenger
         */
        $messenger = $this->cloner->container->get(GhostMessenger::class);
        $messenger->asyncSendRequest($request);
        return $request->response();
    }

    /**
     * 同步消息锁失败.
     *
     * @param GhostRequest $request
     * @return GhostResponse
     */
    protected function lockFail(GhostRequest $request) : GhostResponse
    {
        $this->cloner->noState();
        return $request->output(
            $this->cloner->getAppId(),
            $this->cloner->getApp()->getName(),
            SessionBusyInt::instance()
        );
    }

}