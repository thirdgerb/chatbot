<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Intercom;

use Commune\Protocals\IntercomMessage;


/**
 * # 输入消息的维度
 *
 * @property-read string $hostName              机器人的名称.
 * @property-read string $shellName             消息产生的 shell 名称
 * @property-read string $shellId               消息对应的分身Id. 决定通道
 * @property-read string|null $sessionId        消息所属的 SessionId
 * @property-read string $guestId               对接 Host 的对方 ID
 * @property-read string $guestName             对接 Host 的对方名称.
 *
 * # 更多
 * @see IntercomMessage
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellMsg extends IntercomMessage
{

}