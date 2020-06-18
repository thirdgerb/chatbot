<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Platform;

use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostAdapter
{

    public function getRequest() : GhostRequest;

    public function sendResponse(GhostResponse $response) : void;
}