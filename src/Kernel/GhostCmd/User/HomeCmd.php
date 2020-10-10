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
class HomeCmd extends AbsIntentCmd
{
    const SIGNATURE = 'home';

    const DESCRIPTION = '返回对话起点';

    protected function getIntentName(): string
    {
        return DefaultIntents::GUEST_NAVIGATE_HOME;
    }

}