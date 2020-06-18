<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Api;

use Commune\Blueprint\Framework\App;
use Commune\Blueprint\Framework\Handlers\ApiController;
use Commune\Blueprint\Framework\ReqContainer;
use Commune\Blueprint\Kernel\AppKernel;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Protocals\HostMsg\Convo\ApiMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ApiRunner
{

    public static function runApi(AppKernel $app, ReqContainer $container, AppRequest $request) : ? AppResponse
    {
        $input = $request->getInput();
        $message = $input->getMessage();
        if (!$message instanceof ApiMsg) {
            return null;
        }

        $each = $app->eachProtocalHandler(
            $container,
            $message,
            ApiController::class
        );

        /**
         * @var ApiController $controller
         */
        foreach ($each as $controller) {
            return $controller($request, $message);
        }

        return null;
    }

}