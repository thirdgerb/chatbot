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

/**
 * Shell 的输出消息.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $messageId
 * @property-read Message $message
 * @property-read string $shellName
 * @property-read string $shellId
 * @property-read string $senderId
 * @property-read string $senderName
 * @property-read string $sessionId
 */
interface ShellOutput extends ShellMessage
{
}