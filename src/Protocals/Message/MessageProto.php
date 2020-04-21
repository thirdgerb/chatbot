<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Message;

use Commune\Support\Message\Protocal;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read float $createdAt      消息发送的时间戳, 精确到毫秒最多.
 * @property-read string $level         消息的级别.
 */
interface MessageProto extends Protocal
{
}