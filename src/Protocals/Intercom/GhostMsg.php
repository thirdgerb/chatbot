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
 *
 * ## 上下文维度
 * @property-read string $cloneId               Ghost 分身的 Id. 为空则是 shellId
 * @property-read string $sessionId             消息所属的 SessionId
 *
 * ## 用户在 Clone 内的信息
 * @property-read string $guestId               消息投递的目标用户Id. 为空则与 senderId 一致
 * @property-read string $guestName             消息投递的目标用户名. 为空则与 senderName 一致.
 *
 * ## Shell 相关信息
 * @property-read string $senderId              创建输入消息的 SenderId
 * @property-read string $shellName             创建输入消息所属的 Shell
 * @property-read string $shellId               创建输入消息所属的 ShellId
 *
 */
interface GhostMsg extends IntercomMessage
{

    public function getGuestId() : string;

    public function getGuestName() : string;

    public function getCloneId() : string;

}