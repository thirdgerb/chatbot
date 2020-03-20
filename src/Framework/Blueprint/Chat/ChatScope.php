<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Chat;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ChatScope
{

    /**
     * 机器人的唯一身份标识.
     * 拥有相同 ChatName, 无论 shell、chatId 不同,都是同一个机器人.
     * @return string
     */
    public function getChatName() : string;

    /**
     * 对于机器人而言, 每一个 ChatId 对应一个独立的分身.
     * 不同的 Shell 可以属于同一个 ChatId
     * 所有 Shell 的消息都从属于这个 ChatId.
     *
     * @return string
     */
    public function getChatId() : string;

    /**
     * 当前 Chat 所属的 shell
     * @return string
     */
    public function getShellId() : string;

    /**
     * Shell 所属的平台
     * @return string
     */
    public function getPlatformId() : string;

    /**
     * 当前用户的 Id
     * @return string
     */
    public function getUserId() : string;

    /**
     * 发送消息的信道.
     * 同一个 ChatId,同一个 Shell,仍可能有不同的信道.
     * @return string
     */
    public function getChannelId() : string;

}