<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\Protocals;

use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\AppResponse;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class LogContext
{
    public static function requestToContext(AppRequest $request) : array
    {
        $context = [
            'traceId' => $request->getTraceId(),
            'batchId' => $request->getBatchId(),
            'sessionId' => $request->getSessionId(),
        ];

        if ($request instanceof GhostRequest) {
            $context['fromApp'] = $request->getFromApp();
            $context['fromSession'] = $request->getFromSession();
            $context['convoId'] = $request->getInput()->getConvoId();
        }

        return $context;
    }

    public static function responseToContext(AppResponse $response) : array
    {
        $context = [
            'traceId' => $response->getTraceId(),
            'batchId' => $response->getBatchId(),
            'sessionId' => $response->getSessionId(),

        ];

        return $context;
    }

}