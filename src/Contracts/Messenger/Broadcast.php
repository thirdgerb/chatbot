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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Broadcast
{
    public function publish(
        string $shellName,
        string $sessionId,
        string $batchId
    ) : bool;

    public function subscribe(
        callable $callback,
        string $shellName,
        string $sessionId = null
    ) : void;

}