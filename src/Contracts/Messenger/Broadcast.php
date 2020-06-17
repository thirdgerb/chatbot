<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts\Messenger;

use Commune\Blueprint\Kernel\Protocals\GhostResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Broadcast
{
    public function publish(GhostResponse $ghostResponse) : bool;

    public function subscribe(
        callable $callback,
        string $shellName,
        string $sessionId = null
    ) : void;

}