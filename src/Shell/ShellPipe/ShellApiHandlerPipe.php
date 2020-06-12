<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Shell\ShellPipes;

use Commune\Blueprint\Shell\Requests\ShellRequest;
use Commune\Blueprint\Shell\Requests\ShlInputRequest;
use Commune\Blueprint\Shell\Responses\ShellResponse;
use Commune\Blueprint\Shell\Responses\ShlInputResponse;
use Commune\Protocals\HostMsg\Convo\ApiMsg;
use Commune\Shell\ShellPipe\AShellInputPipe;


/**
 * Api 请求的管道. 将 Ghost 作为 API Server 来响应.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ShellApiHandlerPipe extends AShellInputPipe
{

    /**
     * @param ShlInputRequest $request
     * @param \Closure $next
     * @return ShlInputResponse
     */
    protected function doHandle(ShellRequest $request, \Closure $next): ShellResponse
    {
        $message = $request->getInput()->getMessage();

        if (!$message instanceof ApiMsg) {
            return $next($request);
        }

        $handler = $this->session->shell->getApiHandler(
            $this->session->container,
            $message
        );

        if (isset($handler)) {
            return $handler($request, $message);
        }

        return $next($request);
    }

}