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

use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Contracts\Api\ApiController;
use Commune\Protocals\HostMsg\ApiMsg;
use Commune\Support\Utils\TypeUtils;


/**
 * Api 请求的管道. 将 Ghost 作为 API Server 来响应.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CloneApiHandlePipe extends AClonePipe
{

    protected function doHandle(GhostRequest $request, \Closure $next): GhostResponse
    {
        if ($request->isAsync()) {
            return $next($request);
        }

        $message = $request->getInput()->getMessage();
        if (!$message instanceof ApiMsg) {
            return $next($request);
        }

        // 无状态请求.
        $this->cloner->noState();

        // 运行获取 controller
        $response = $this->runApi($request);

        // ghost 不允许处理 api message.
        if (!isset($response)) {
            return $request->response(AppResponse::HANDLER_NOT_FOUND);
        }

        if (!$response instanceof GhostResponse) {
            $type = TypeUtils::getType($response);
            $this->cloner->logger->error(
                __METHOD__
                . ' invalid response from api handler, api '
                . $message->getApiName()
                . ", $type given"
            );
        }


        return $response;
    }

    protected function runApi(GhostRequest $request)
    {
        $input = $request->getInput();
        $message = $input->getMessage();
        if (!$message instanceof ApiMsg) {
            return null;
        }

        $controller = $this->cloner->ghost->firstProtocalHandler(
            $this->cloner->container,
            $message,
            ApiController::class
        );

        if (empty($controller)) {
            return null;
        }

        /**
         * @var ApiController $controller
         */
        return $controller($request, $message);
    }

}