<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype\Convo;

use Commune\Message\Blueprint\Convo\UnsupportedMsg;
use Commune\Message\Prototype\AMessage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IUnsupported extends AMessage implements UnsupportedMsg
{
    public static function babelUnSerialize(string $input)
    {
        // TODO: Implement babelUnSerialize() method.
    }

    public function babelSerialize(): string
    {
        // TODO: Implement babelSerialize() method.
    }

    public function getData(): array
    {
        return [];
    }


}