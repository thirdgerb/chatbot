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

use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
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