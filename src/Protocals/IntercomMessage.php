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

use Commune\Support\Protocal\Protocal;

/**
 * 机器人内部通信用的消息.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * # 内部通信的基础协议
 *
 * ## ID
 * @property-read string $messageId         消息的唯一 ID
 * @property-read string $batchId           消息的批次. 为空则是 MessageId
 *
 * # 消息体
 * @property-read HostMsg $message          消息体
 *
 * # 时间戳
 * @property-read float  $createdAt         创建时间, 精确到毫秒
 * @property-read float  $deliverAt         发送时间.
 */
interface IntercomMessage extends Protocal
{
    public function getBatchId() : string;
}
