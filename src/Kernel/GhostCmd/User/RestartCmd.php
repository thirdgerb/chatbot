<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Kernel\GhostCmd\User;

use Commune\Protocals\HostMsg\DefaultIntents;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RestartCmd extends AbsIntentCmd
{
    const SIGNATURE = 'restart';

    const DESCRIPTION = '当前对话语境从头开始';

    protected function getIntentName(): string
    {
        return DefaultIntents::GUEST_NAVIGATE_RESTART;
    }


}