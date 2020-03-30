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
use Commune\Message\Blueprint\Internal\InputMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RSRequest implements GhtRequest
{
    public function validate(): bool
    {
        return true;
    }

    public function fetchIncoming(): InputMsg
    {
        // TODO: Implement fetchIncoming() method.
    }


}