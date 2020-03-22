<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 */
interface Chat
{

    /**
     * 插入一个输入消息到 buffer 里.
     * 严谨的时候要这么做, 才不会丢失输入消息
     *
     * @param string $chatId
     * @param array $messages
     * @param array $shellNames
     * @return int
     */
    public function pushIncoming(
        string $chatId,
        array $messages,
        array $shellNames = []
    ) : int;

    /**
     * 取出一条输入消息, 用于后续处理.
     *
     * @param string $chatId
     * @param string $shellName
     * @return IncomingMessage|null
     */
    public function popIncoming(string $chatId, string $shellName) : ? IncomingMessage;

    /**
     * 将响应广播到多个 Shell
     *
     * @param string $chatId
     * @param ReactionMessage[] $reactions
     * @param string[] $shellNames 为空表示所有的.
     * @return int
     */
    public function publishReactions(
        string $chatId,
        array $reactions,
        array $shellNames = []
    ) : int;

    /**
     * 监听 Shell 的回复.
     *
     * @param string $chatId
     * @param array $shellNames
     * @return ReactionMessage 返回的消息
     */
    public function subscribe(
        string $chatId,
        array $shellNames = []
    ) : ? ReactionMessage;


    public function lock(string $chatId) : bool;

    public function unlock(string $chatId) : bool ;
}