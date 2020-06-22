<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform;

use Commune\Blueprint\Kernel\Handlers\ShellInputReqHandler;
use Commune\Blueprint\Kernel\Protocals\AppRequest;
use Commune\Blueprint\Kernel\Protocals\ShellInputRequest;
use Commune\Blueprint\Kernel\Protocals\ShellOutputResponse;
use Commune\Blueprint\Platform\Adapter;
use Commune\Blueprint\Shell;
use Commune\Blueprint\Platform\Adapters\ShellInputAdapter;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class PlatformHandler
{

    public static function shellHandleRequest(
        Shell $shell,
        Adapter $adapter,
        AppRequest $request
    ) : bool
    {
        $match = $adapter instanceof ShellInputAdapter
            && $request instanceof ShellInputRequest;

        if (!$match) {
            return false;
        }

        /**
         * @var ShellOutputResponse $response
         */
        $response = $shell->handleRequest($request, ShellInputReqHandler::class);

        $adapter->sendResponse($response);
        unset($adapter);
        return true;
    }


}