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
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellInputResponse;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Contracts\Messenger\MessageDB;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OutputAsyncFillMessagesPipe extends AShellPipe
{
    protected function handleInput(
        ShellInputRequest $request,
        \Closure $next
    ): ShellInputResponse
    {
        return $next($request);
    }

    protected function handleOutput(
        ShellOutputRequest $request,
        \Closure $next
    ): ShellOutputResponse
    {
        if (!$request->isAsync()) {
            return $next($request);
        }

        $outputs = $request->getOutputs();
        if (!empty($outputs)) {
            return $next($request);
        }

        /**
         * @var MessageDB $messageDB
         */
        $messageDB = $this->session->container->make(MessageDB::class);

        $outputs = $messageDB->fetchBatch($request->getBatchId());
        $request->setOutputs($outputs);

        return $next($request);
    }


}