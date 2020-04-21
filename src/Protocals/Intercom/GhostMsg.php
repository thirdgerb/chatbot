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
 * Ghost 收到的输入消息.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * # 默认属性
 * @see IntercomMessage
 *
 * # 机器人名称
 *
 * @property-read string $hostName              机器人的名称.
 *
 * # 可替换属性
 * # 以下几个属性可能在上下文中被替换, 从而让 Shell 之间对接起来.
 *
 * @property-read string $cloneId               Ghost 分身的 Id
 * @property-read string $sessionId             消息所属的 SessionId
 * @property-read string $guestId               对接 Host 的对方 Id
 * @property-read string $guestName             对接 Host 的对方名称.
 *
 */
interface GhostMsg extends IntercomMessage
{

}