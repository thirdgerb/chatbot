<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\ShellPipes;

use Commune\Blueprint\Exceptions\Runtime\BrokenRequestException;
use Commune\Blueprint\Exceptions\Runtime\BrokenSessionException;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellInputResponse;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Contracts\Log\ExceptionReporter;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellTryCatchPipe extends AShellPipe
{
    protected function handleInput(
        ShellInputRequest $request,
        \Closure $next
    ): ShellInputResponse
    {
        try {

            return $next($request);

        } catch (BrokenSessionException $e) {
            $this->report($e);
            return $request->response(
                AppResponse::HOST_SESSION_FAIL,
                $e->getMessage()
            );

        } catch (BrokenRequestException $e) {
            $this->report($e);
            return $request->response(
                AppResponse::HOST_REQUEST_FAIL,
                $e->getMessage()
            );
        }
    }


    protected function handleOutput(
        ShellOutputRequest $request,
        \Closure $next
    ): ShellOutputResponse
    {
        try {

            return $next($request);

        } catch (BrokenSessionException $e) {
            $this->report($e);
            return $request->response(
                AppResponse::HOST_SESSION_FAIL,
                $e->getMessage()
            );

        } catch (BrokenRequestException $e) {
            $this->report($e);
            return $request->response(
                AppResponse::HOST_REQUEST_FAIL,
                $e->getMessage()
            );
        }
    }

    protected function report(\Throwable $e) : void
    {
        /**
         * @var ExceptionReporter $reporter
         */
        $reporter = $this->session->container->make(ExceptionReporter::class);
        $reporter->report($e);
    }

}