<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Messenger\Fake;

use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Contracts\Messenger\Broadcaster;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class EmptyBroadcaster implements Broadcaster
{
    public function publish(
        GhostRequest $request,
        GhostResponse $response,
        array $routes
    ): void
    {
    }

    public function subscribe(
        callable $callback,
        string $shellId,
        string $shellSessionId = null
    ): void
    {
    }


}