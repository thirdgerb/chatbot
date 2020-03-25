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
interface Policy
{
    /**
     * 没有消息表示拥有权限.
     * 有消息表示拒绝的消息.
     *
     * @param array $payload
     * @return Message|null
     */
    public function invoke(array $payload = []) : ? Message;
}