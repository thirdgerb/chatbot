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

use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Broadcaster
{

    /**
     * @param string $shellId
     * @param string $sessionId
     * @param ShellOutputRequest $request
     */
    public function publish(
        string $shellId,
        string $sessionId,
        ShellOutputRequest $request
    ) : void;

    /**
     * @param callable $callback        传入参数, ShellOutputRequest
     * @param string $shellId
     * @param string|null $sessionId
     */
    public function subscribe(
        callable $callback,
        string $shellId,
        string $sessionId = null
    ) : void;

}