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

use Commune\Messages\Blueprint\ConvoMsg;
use Commune\Shell\Blueprint\Reaction\Reaction;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ChatQueue
{

    /**
     * 插入一个输入消息到 buffer 里.
     * 严谨的时候要这么做, 才不会丢失输入消息
     *
     * @param ChatScope $chatInfo
     * @param ConvoMsg[] $messages
     * @return int
     */
    public function pushIncoming(ChatScope $chatInfo, array $messages) : int;

    /**
     * 取出一条输入消息, 用于后续处理.
     *
     * @param ChatScope $chatInfo
     * @return IncomingMessage|null
     */
    public function popIncoming(ChatScope $chatInfo) : ? IncomingMessage;


    /**
     * 推送同步响应给当前的 shell
     *
     * @param ChatScope $scope
     * @param  Reaction[] $reactions
     * @return int
     */
    public function sendReactions(
        ChatScope $scope,
        array $reactions
    ) : int;


    /**
     * 将响应广播到多个 Shell
     *
     * @param string $chatId
     * @param Reaction[] $reactions
     * @param string[] $shellNames
     * @return int
     */
    public function broadcastReactions(
        string $chatId,
        array $reactions,
        array $shellNames = []
    ) : int;

    /**
     * 拿到当前 Shell 的回复.
     * @param string $chatId
     * @param string $shellId
     * @return ReactionMessage[]
     */
    public function popReactions(string $chatId, string $shellId) : array;

    /**
     * 取出任何一条回复消息, 无论是哪个 shell, 哪个 chat 的
     * 前提是能做到.
     *
     * @return ReactionMessage|null
     */
    public function popAnyReaction() : ? ReactionMessage;
}