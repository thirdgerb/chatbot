<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\MessengerFaker;

use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;
use Commune\Contracts\Messenger\Broadcaster;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class FakeBroadcaster implements Broadcaster
{
    /**
     * @var callable[]
     */
    protected $listeners = [];

    public function subscribe(
        callable $callback,
        string $shellId,
        string $sessionId = null
    ): void
    {
    }

    public function publish(
        string $shellId,
        ShellOutputRequest $request
    ): void
    {
    }


}