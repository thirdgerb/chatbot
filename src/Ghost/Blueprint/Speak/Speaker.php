<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Speak;
use Commune\Message\Convo\ConvoMsg;
use Commune\Message\Directive\DirectiveMsg;
use Commune\Message\Message;
use Commune\Message\Reaction\ReactionMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Speaker
{
    /**
     * 发送一个同步的响应消息, 给当前的 Shell
     *
     * @param ReactionMsg|ConvoMsg|DirectiveMsg|Message $message
     */
    public function reply(Message $message) : void;

    /**
     * 广播消息, 给所有的 shell
     *
     * @param Message $message
     */
    public function broadcast(Message $message) : void;

    /**
     * 投递消息到指定的 Chat 和指定的 shell
     *
     * @param string $chatId
     * @param Message $message
     * @param array $shellNames
     */
    public function deliver(
        string $chatId,
        Message $message,
        array $shellNames = []
    ) : void;



}