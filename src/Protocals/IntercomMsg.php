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
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface IntercomMsg extends Message, Protocal
{

    /**
     * 检查是否不合法.
     * @return null|string
     */
    public function isInvalid() : ? string;

    /*------- properties -------*/

    public function getShellName() : string;

    /**
     * 传输消息的唯一ID
     * @return string
     */
    public function getMessageId() : string;

    /**
     * @return string
     */
    public function getBatchId() : string;

    /**
     * 消息的追踪 ID.
     * @return string
     */
    public function getTraceId() : string;

    /**
     * 会话的 ID
     * @return string
     */
    public function getSessionId() : string;

    /**
     * 一次多轮对话的 ID
     * @return string
     */
    public function getConversationId() : string;

    /**
     * 用户的身份Id
     * @return string
     */
    public function getGuestId() : string;

    /**
     * 用户的名称.
     * @return string
     */
    public function getGuestName() : string;

    /**
     * 消息体
     * @return HostMsg
     */
    public function getMessage() : HostMsg;

    /**
     * 精确到秒
     * @return int
     */
    public function getCreatedAt() : int;

    /**
     * 精确到秒
     * @return int
     */
    public function getDeliverAt() : int;

    /*------- setter -------*/

    /**
     * @param HostMsg $message
     * @return void
     */
    public function setMessage(HostMsg $message) : void;

    public function setConvoId(string $convoId) : void;

    /*------- methods -------*/

    /**
     * 衍生新的消息.
     *
     * @param HostMsg|null $message
     * @param string|null $shellName
     * @param string|null $sessionId
     * @param string|null $convoId
     * @param string|null $guestId
     * @param string|null $guestName
     * @param int|null $deliverAt
     * @return IntercomMsg
     */
    public function divide(
        HostMsg $message = null,
        string $shellName = null,
        string $sessionId = null,
        string $convoId = null,
        string $guestId = null,
        string $guestName = null,
        int $deliverAt = null
    ) : IntercomMsg;


    /*------- hostMsg -------*/

    /**
     * @param string $hostMessageType
     * @return bool
     */
    public function isMsgType(string $hostMessageType) : bool;

    /**
     * @return string
     */
    public function getMsgText() : string;

    /**
     * @param string $renderId
     * @return string
     */
    public function getMsgRenderId(string $renderId) : string;

    /**
     * @return string
     */
    public function getNormalizedText() : string;

}
