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

use Commune\Support\Message\Message;
use Commune\Support\Protocal\Protocal;

/**
 * 机器人内部通信用的消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface IntercomMsg extends Message, Protocal
{
    /**
     * 传输消息的唯一ID
     * @return string
     */
    public function getMessageId() : string;

    /**
     * 消息的批次 ID. 回复消息和输入消息是同一批.
     * @return string
     */
    public function getBatchId() : string;

    /**
     * 消息体
     * @return HostMsg
     */
    public function getMessage() : HostMsg;

    /**
     * 精确到毫秒
     * @return float
     */
    public function getCreatedAt() : float;

    /**
     * 精确到毫秒
     * @return float
     */
    public function getDeliverAt() : float;
}
