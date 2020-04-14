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
 * @property-read string $traceId               链路 ID
 * @property-read string $cloneId               消息所属的机器人 ID
 * @property-read string $shellId               投递的 shellId
 * @property-read string $shellName             投递的目标 shell
 *
 * @property-read ShellMessage $shellMessage
 * @property-read int $deliverAt                发送的时间
 */
interface GhostOutput extends GhostMessage
{
}