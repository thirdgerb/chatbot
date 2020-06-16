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

use Commune\Blueprint\Kernel\Protocals\GhostResponse;
use Commune\Blueprint\Kernel\Protocals\ShellOutputRequest;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellOutputReceiver extends AppProtocalHandler
{
    /**
     * @param GhostResponse $protocal
     * @return ShellOutputRequest
     */
    public function __invoke($protocal);

}