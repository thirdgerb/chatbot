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


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $chatId
 * @property-read string $shellName
 * @property-read string $messageId
 * @property-read string $traceId               链路追踪的ID
 * @property-read ShellMsg $shellMessage
 * @property-read int $dlt                发送的时间
 */
interface GhostOutput extends GhostMsg
{
}