<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\Shell\Tcp;

use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Platform\Libs\SwlAsync\TcpAdapterAbstract;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwlAsyncTextShellAdapter extends TcpAdapterAbstract
{

    protected function isValidRequest(AppRequest $request): bool
    {
        return $request instanceof ShellInputRequest;
    }

    protected function isValidResponse(AppResponse $response): bool
    {
        return $response instanceof ShellOutputResponse;
    }


}