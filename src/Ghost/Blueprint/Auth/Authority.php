<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Auth;

use Commune\Message\Message;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Authority
{
    public function allow(string $policy, array $payload) : ? Message;

}