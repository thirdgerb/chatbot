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
use Commune\Framework\Event\FinishRequest;
use Commune\Message\Host\SystemInt\SessionBusyInt;

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

        $ttl = $this->cloner->config->sessionLockerExpire;
        // 锁 clone
        if ($this->cloner->lock($ttl)) {
            // 注册解锁逻辑.
            $this->cloner->listen(
                FinishRequest::class,
                function(Cloner $cloner){
                    $cloner->unlock();
                }
            );

            return $next($request);
        }


        // 异步请求和同步请求最大的区别在于锁失败后的处理逻辑
        return $request->isAsync()
            ? $this->asyncLockFail($request)
            : $this->lockFail($request);
    }

    /**
     * 异步消息锁失败.
     * @param GhostRequest $request
     * @return GhostResponse
     */
    protected function asyncLockFail(GhostRequest $request) : GhostResponse
    {
        $this->cloner->asyncInput($this->cloner->input);
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
        return $request->output(new SessionBusyInt());
    }

}