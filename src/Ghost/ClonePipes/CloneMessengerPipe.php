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

use Closure;
use Commune\Blueprint\Exceptions\HostRuntimeException;
use Commune\Blueprint\Ghost\Cloner\ClonerStorage;
use Commune\Blueprint\Ghost\Request\GhostRequest;
use Commune\Blueprint\Ghost\Request\GhostResponse;
use Commune\Message\Host\SystemInt\RequestFailInt;
use Commune\Message\Host\SystemInt\SessionFailInt;
use Commune\Protocals\HostMsg\Convo\UnsupportedMsg;
use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Exceptions\Runtime\BrokenRequestException;
use Commune\Blueprint\Exceptions\Runtime\BrokenSessionException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneMessengerPipe extends AClonePipe
{

    protected function doHandle(GhostRequest $request, Closure $next) : GhostResponse
    {
        $message = $request->getInput()->getMessage();

        if ($message instanceof UnsupportedMsg) {
            return $request->fail(AppResponse::NO_CONTENT);
        }

        try {

            $response = $next($request);
            $this->resetFailureCount();
            return $response;

        } catch (BrokenSessionException $e) {

            return $this->quitSession($request, $e);

        } catch (BrokenRequestException $e) {

            return $this->requestFail($request, $e);
        }
    }

    protected function requestFail(
        GhostRequest $request,
        HostRuntimeException $e
    ) : GhostResponse
    {

        $storage = $this->cloner->storage;
        $times = $storage[ClonerStorage::REQUEST_FAIL_TIME_KEY] ?? 0;
        $times ++;
        if ($times >= $this->cloner->config->maxRequestFailTimes) {
            return $this->quitSession($request, $e);
        }

        $storage[ClonerStorage::REQUEST_FAIL_TIME_KEY] = $times;
        $this->cloner->output(
            $this->cloner->input->output(new RequestFailInt($e->getMessage()))
        );
        return $request->success($this->cloner);

    }

    protected function quitSession(
        GhostRequest $request,
        HostRuntimeException $e
    ) : GhostResponse
    {
        $message = new SessionFailInt(
            $e->getMessage()
        );

        $this->cloner->output(
            $this->cloner->input->output($message)
        );
        $this->cloner->quit();
        $this->resetFailureCount();

        return $request->success($this->cloner);
    }

    protected function resetFailureCount() : void
    {
        $storage = $this->cloner->storage;
        $storage->offsetUnset(ClonerStorage::REQUEST_FAIL_TIME_KEY);
    }
}