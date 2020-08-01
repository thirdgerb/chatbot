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
     * @return string
     *
     * @example Commune.Protocals.Intercom.InputMsg
     * @example Commune.Protocals.Intercom.OutputMsg
     */
    public function getProtocalId(): string;

    /**
     * 检查是否不合法.
     * @return null|string
     */
    public function isInvalid() : ? string;

    /*------- properties -------*/

    /**
     * 消息所属的 SessionId.
     * 与多轮对话的逻辑有关.
     *
     * @return string
     */
    public function getSessionId() : string;

    /**
     * 一次多轮对话的 ID.
     * 可以用于定位消息.
     *
     * 在消息的座标维度里位置如下 :
     *
     * - HostId     : 机器人的名称.
     * - SessionId  : 机器人的分身, 统筹这个分身的所有对话历史
     * - ConvoId    : 一次多轮交互的 ID, 包含若干次多轮对话内容.
     * - BatchId
     * - MessageId
     *
     * @return string
     */
    public function getConvoId() : string;

    /**
     * 场景
     * @return string
     */
    public function getScene() : string;

    /**
     * 消息的批次 ID
     * @return string
     */
    public function getBatchId() : string;

    /**
     * 传输消息的唯一ID
     * @return string
     */
    public function getMessageId() : string;

    /**
     * 创建消息的用户身份.
     * @return string
     */
    public function getCreatorId() : string;

    /**
     * 创建消息的用户名称.
     * @return string
     */
    public function getCreatorName() : string;

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

    /**
     * @param string $convoId
     */
    public function setConvoId(string $convoId) : void;

    /*------- methods -------*/

    /**
     * 衍生新的消息.
     *
     * @param HostMsg $message
     * @param string $sessionId
     * @param string|null $convoId
     * @param string|null $creatorId
     * @param string|null $creatorName
     * @param int|null $deliverAt
     * @param string|null $scene
     *
     * @return static
     */
    public function divide(
        HostMsg $message,
        string $sessionId,
        string $convoId = null,
        string $creatorId = null,
        string $creatorName = null,
        int $deliverAt = null,
        string $scene = null
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
     * @return string
     */
    public function getNormalizedText() : string;

}
