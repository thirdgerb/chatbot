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

use Commune\Framework\Exceptions\InvalidClassException;
use Commune\Message\Blueprint\Message;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Authority
{
    /**
     * @param string $policy
     * @param array $payload
     * @return Message|null
     * @throws InvalidClassException
     */
    public function forbid(string $policy, array $payload) : ? Message;

    public function allow(string $policy, array $payload) : bool;

}