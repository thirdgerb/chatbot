<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Intercom;

use Commune\Message\Blueprint\Message;
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Babel\BabelSerializable;


/**
 * 在 Shell 与 Client 之间传输的消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellMessage extends BabelSerializable, ArrayAndJsonAble
{
    public function replace(Message $message) : void;

    /**
     * 衍生出 Scope 相同的消息.
     * @param Message $message
     * @return ShellOutput
     */
    public function derive(Message $message) : ShellOutput;
}