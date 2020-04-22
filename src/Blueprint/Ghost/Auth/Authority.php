<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Auth;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Authority
{

    /**
     * @param string $policy
     * @param array $payload
     * @return bool
     */
    public function allow(string $policy, array $payload) : bool;

}