<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Intercom;

use Commune\Blueprint\ConvoMsg;

/**
 * 在 Shell 上生成并传输的消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ShellMessage
{

    /*------ 属性 ------*/

    /**
     * 消息唯一 ID
     * @return string
     */
    public function getMessageId() : string;

    /**
     * Shell 上的会话 ID, 也决定消息投递的渠道.
     * 在 Wechat 上会话 ID 是用户唯一决定的.
     * 在 DingDing 上则是由群来决定的.
     *
     * SessionId 也将决定消息的投递渠道.
     *
     * @return string
     */
    public function getSessionId() : string;


    /**
     * 发送者的 ID
     * @return string
     */
    public function getSenderId() : string;

    /**
     * 发送者的名称
     * @return string
     */
    public function getSenderName() : string;

    /**
     * 消息体
     * @return ConvoMsg
     */
    public function getMessage() : ConvoMsg;

    /**
     * 消息创建时间.
     * @return int
     */
    public function getCreatedAt() : int;
}