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
 * @property-read string $chatId            会话 ID
 * @property-read string $shellName         决定发送给哪个 Shell
 * @property-read string $messageId
 * @property-read ShellMsg $shellMessage    发送的内容
 * @property-read int $deliverAt            发送的时间
 */
interface GhostOutput extends GhostMsg
{
}