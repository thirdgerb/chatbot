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

use Commune\Blueprint\Framework\Request\AppRequest;
use Commune\Blueprint\Shell\Requests\ShellRequest;
use Commune\Blueprint\Shell\Requests\ShlInputRequest;
use Commune\Blueprint\Shell\Responses\ShellResponse;
use Commune\Blueprint\Shell\Responses\ShlInputResponse;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AShellInputPipe extends AShellPipe
{
    protected function isValidRequest(AppRequest $request): bool
    {
        return $request instanceof ShlInputRequest;
    }

    /**
     * @param ShlInputRequest $request
     * @param \Closure $next
     * @return ShlInputResponse
     */
    abstract protected function doHandle(ShellRequest $request, \Closure $next): ShellResponse;



}