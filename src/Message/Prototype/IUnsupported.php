<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype;

use Commune\Message\Blueprint\UnsupportedMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IUnsupported extends AMessage implements UnsupportedMsg
{
    public function __sleep(): array
    {
        return ['createdAt'];
    }

    public function isEmpty(): bool
    {
        return false;
    }


}