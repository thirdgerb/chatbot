<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Kernel\Handlers;

use Commune\Blueprint\Kernel\Protocols\GhostRequest;
use Commune\Blueprint\Kernel\Protocols\GhostResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostRequestHandler
{

    public function __invoke(GhostRequest $request) : GhostResponse;

}