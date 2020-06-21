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

use Closure;
use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Exceptions\CommuneRuntimeException;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Contracts\Log\ExceptionReporter;
use Commune\Message\Host\SystemInt\RequestFailInt;
use Commune\Message\Host\SystemInt\SessionFailInt;
use Commune\Message\Host\SystemInt\SessionQuitInt;
use Commune\Blueprint\Exceptions\Runtime\BrokenRequestException;
use Commune\Blueprint\Exceptions\Runtime\BrokenSessionException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneGuardPipe extends AClonePipe
{
    /**
     * @var string[]
     */
    protected $unsupportedMessages = [];

    protected function doHandle(GhostRequest $request, Closure $next) : GhostResponse
    {
        $message = $request->getInput()->getMessage();

        if (CommuneEnv::isDebug()) {
            $text = $message->getText();
            $this->cloner->logger->debug("receive message : \"$text\"");
        }

        try {

            $response = $next($request);
            $this->resetFailureCount();

            return $response;

        } catch (BrokenSessionException $e) {
            $this->report($e);
            $this->cloner->noState();
            return $this->quitSession($request, $e);

        } catch (BrokenRequestException $e) {
            $this->report($e);
            $this->cloner->noState();
            return $this->requestFail($request, $e);
        }
    }

    protected function report(\Throwable $e) : void
    {
        /**
         * @var ExceptionReporter $expHandler
         */
        $expHandler = $this->cloner->container->get(ExceptionReporter::class);
        $expHandler->report($e);
    }

    protected function requestFail(
        GhostRequest $request,
        CommuneRuntimeException $e
    ) : GhostResponse
    {

        $storage = $this->cloner->storage;
        $times = $storage->requestFailTimes ?? 0;
        $times ++;
        if ($times >= $this->cloner->config->maxRequestFailTimes) {
            return $this->quitSession($request, $e);
        }

        $storage->requestFailTimes = $times;

        return $request->output(
            $this->cloner->getAppId(),
            $this->cloner->getApp()->getName(),
            RequestFailInt::instance($e->getMessage())
        );
    }

    protected function quitSession(
        GhostRequest $request,
        CommuneRuntimeException $e
    ) : GhostResponse
    {
        $messages = [
            SessionFailInt::instance($e->getMessage()),
            SessionQuitInt::instance(),
        ];

        $this->cloner->endConversation();
        $this->resetFailureCount();

        return $request->output(...$messages);
    }

    protected function resetFailureCount() : void
    {
        $storage = $this->cloner->storage;
        $storage->requestFailTimes = 0;
    }
}