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
class BroadcasterByEvent implements Broadcaster
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
        $sessionId = $sessionId ?? '';
        $id = "$shellId::$sessionId";
        $this->listeners[$id] = $callback;
    }

    public function publish(ShellOutputRequest $request): void
    {
        $shellId = $request->getShellId();
        $sessionId = $request->getSessionId();

        $id1 = "$shellId::$sessionId";
        $id2 = "$shellId::";
        $listener = $this->listeners[$id1] ?? $this->listeners[$id2] ?? null;

        if (isset($listener)) {
            $listener($request);
        }
    }


}