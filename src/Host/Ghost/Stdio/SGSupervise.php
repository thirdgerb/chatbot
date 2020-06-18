<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Host\Ghost\Stdio;

use Commune\Blueprint\Framework\Auth\Supervise;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SGSupervise implements Supervise
{
    public function invoke(array $payload = []): bool
    {
        return true;
    }


}