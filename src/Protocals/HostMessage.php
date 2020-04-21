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

use Commune\Protocals\Message\MessageProto;
use Commune\Support\Message\Message;

/**
 * Host 对消息体的基本抽象.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface HostMessage extends Message, MessageProto
{
}