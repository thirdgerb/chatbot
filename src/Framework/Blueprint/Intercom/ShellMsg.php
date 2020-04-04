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
 * 在 Shell 之间传输的消息.
 *
 * 可以来自于 Shell A, 发生给 Ghost, 然后也会广播到 Shell B/C/D
 * 也会来自于 Ghost, 但携带着原生 Shell 相关的信息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Message $message
 * @property-read ShellScope $scope
 */
interface ShellMsg extends BabelSerializable, ArrayAndJsonAble
{

    public function replace(Message $message) : void;

    /**
     * 衍生出 Scope 相同的消息.
     * @param Message $message
     * @return ShellOutput
     */
    public function derive(Message $message) : ShellOutput;
}