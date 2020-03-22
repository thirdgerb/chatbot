<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Conversation;

use Commune\Framework\Blueprint\Chat\ReactionMessage;
use Commune\Message\Blueprint\Reaction;

/**
 * 消息发送工具. 用来 buffer 一个 Conversation 的所有回复.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Messenger
{
    /**
     * 发送一个同步信息.
     * @param Reaction $reaction
     */
    public function reply(Reaction $reaction) : void;

    /**
     * 广播消息, 到各个 shell 的消息通道.
     * @param string $chatId
     * @param Reaction $reaction
     * @param array $shellNames
     */
    public function broadcast(
        string $chatId,
        Reaction $reaction,
        array $shellNames = []
    ) : void;

    /**
     * 获取同步的响应.
     * @return ReactionMessage[]
     */
    public function getReplyBuffer() : array;

    /**
     * 获取广播的响应.
     * @return array
     */
    public function getBroadcastBuffer() : array;

}