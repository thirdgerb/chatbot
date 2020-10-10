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

use Commune\Blueprint\Kernel\Protocols\GhostRequest;
use Commune\Contracts\Messenger\GhostMessenger;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class LocalGhostMessenger implements GhostMessenger
{

    protected $chan = [];

    public function asyncSendRequest(GhostRequest $request, GhostRequest ...$requests): void
    {
        array_push($this->chan, $request, ...$requests);
    }

    public function receiveAsyncRequest(): ? GhostRequest
    {
        return array_shift($this->chan);
    }


}