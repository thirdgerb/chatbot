<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals;

use Commune\Support\Message\Message;
use Commune\Support\Message\Protocal;

/**
 * Host 对消息体的基本抽象.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $level         消息的级别.
 */
interface HostMsg extends Message, Protocal
{
}