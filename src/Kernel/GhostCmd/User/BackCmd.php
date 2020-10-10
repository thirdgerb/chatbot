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

use Commune\Protocols\HostMsg\DefaultIntents;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BackCmd extends AbsIntentCmd
{
    const SIGNATURE = 'back';

    const DESCRIPTION = '返回上一轮对话';

    protected function getIntentName(): string
    {
        return DefaultIntents::GUEST_NAVIGATE_BACK;
    }


}