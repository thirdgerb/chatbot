<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\ClonePipes;

use Commune\Blueprint\Kernel\Protocals\CloneRequest;
use Commune\Blueprint\Kernel\Protocals\CloneResponse;
use Commune\Message\Host\SystemInt\SessionBusyInt;

/**
 * 请求锁的管道. 防止高并发裂脑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneLockerPipe extends AClonePipe
{
    /**
     * @param CloneRequest $request
     * @param \Closure $next
     * @return CloneResponse
     * @throws \Exception
     */
    protected function doHandle(CloneRequest $request, \Closure $next): CloneResponse
    {

        if ($this->cloner->isStateless()) {
            return $next($request);
        }


        try {

            if (!$this->cloner->lock($this->cloner->config->sessionLockerExpire)) {
                return $request->output(new SessionBusyInt());
            }
            $response = $next($request);
            $this->cloner->unlock();
            return $response;

        } catch (\Exception $e) {
            $this->cloner->unlock();
            throw $e;
        }
    }


}