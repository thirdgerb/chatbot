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

use Commune\Blueprint\Kernel\Protocals\CloneRequest;
use Commune\Blueprint\Kernel\Protocals\GhostRequest;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhostInputReceiver extends AppProtocalHandler
{
    /**
     * @param GhostRequest $protocal
     * @return GhostResponse|CloneRequest
     */
    public function __invoke($protocal);

}