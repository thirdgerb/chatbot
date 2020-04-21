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

use Commune\Support\Message\Protocal;

/**
 * 内部通信的协议
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $messageId         消息唯一ID
 * @property-read string $batchId           消息的批次. 发出和回复是同一批.
 * @property-read string $hostName          机器人的名称.
 * @property-read string $guestId           对接 Host 的对方 ID
 * @property-read string $guestName         对接 Host 的对方名称.
 *
 * @property-read string $shellName         消息产生的 shell 名称(shell 可能会收到别的 Shell 的消息)
 * @property-read string $cloneId           消息对应的通道 Id. 通道对于 Shell 是唯一的.
 * @property-read string|null $sessionId    消息所属的 SessionId
 * @property-read float $deliverAt          消息发送时间. 精确到 毫秒
 */
interface IntercomProto extends Protocal
{

}