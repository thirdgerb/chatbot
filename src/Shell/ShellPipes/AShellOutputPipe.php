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

use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Shell\Requests\ShellRequest;
use Commune\Blueprint\Shell\Requests\ShlOutputRequest;
use Commune\Blueprint\Shell\Responses\ShellResponse;
use Commune\Blueprint\Shell\Responses\ShlOutputResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AShellOutputPipe extends AShellPipe
{
    protected function isValidRequest(AppRequest $request): bool
    {
        return $request instanceof ShlOutputRequest;
    }

    /**
     * @param ShlOutputRequest $request
     * @param \Closure $next
     * @return ShlOutputResponse
     */
    abstract protected function doHandle(ShellRequest $request, \Closure $next): ShellResponse;



}