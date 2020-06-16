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

use Commune\Blueprint\Kernel\Protocals\CloneResponse;
use Commune\Blueprint\Kernel\Protocals\GhostResponse;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Ghost2ShellMessenger extends AppProtocalHandler
{
    /**
     * @param CloneResponse $protocal
     * @return GhostResponse
     */
    public function __invoke($protocal);

}