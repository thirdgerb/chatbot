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
use Commune\Protocals\HostMsg\Convo\ApiMsg;


/**
 * Api 请求的管道. 将 Ghost 作为 API Server 来响应.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneApiHandlePipe extends AClonePipe
{

    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        $message = $request->getInput()->getMessage();

        if (!$message instanceof ApiMsg) {
            return $next($request);
        }

        $handler = $this->cloner->ghost->getApiHandler(
            $this->cloner->container,
            $message
        );

        if (isset($handler)) {
            return $handler($request, $message);
        }

        return $next($request);
    }


}