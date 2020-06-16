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

use Commune\Blueprint\Framework\Request\AppResponse;
use Commune\Blueprint\Kernel\Protocals\CloneRequest;
use Commune\Blueprint\Kernel\Protocals\CloneResponse;
use Commune\Framework\Api\ApiRunner;
use Commune\Protocals\HostMsg\Convo\ApiMsg;


/**
 * Api 请求的管道. 将 Ghost 作为 API Server 来响应.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneApiHandlePipe extends AClonePipe
{

    protected function doHandle(CloneRequest $request, \Closure $next): CloneResponse
    {
        $response = ApiRunner::runApi(
            $this->cloner->ghost,
            $this->cloner->container,
            $request
        );

        if (!isset($response)) {
            return $next($request);
        }

        if (!$response instanceof CloneResponse) {
            $this->logger->error(
                __METHOD__
                . ' invalid response from api handler, message is '
                . $request->getInput()->getMessage()->toJson()
            );

            return $request->fail(AppResponse::HOST_LOGIC_ERROR);
        }

        return $response;
    }


}