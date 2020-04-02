<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Platforms\ReactStdio;

use Commune\Ghost\Contracts\GhtRequest;
use Commune\Framework\Blueprint\Intercom\ShellInput;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RSRequest implements GhtRequest
{
    public function validate(): bool
    {
        return true;
    }

    public function fetchIncoming(): ShellInput
    {
        // TODO: Implement fetchIncoming() method.
    }


}