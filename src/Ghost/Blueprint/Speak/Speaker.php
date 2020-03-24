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
     * 发送一个响应给相关 shell
     *
     * @param string $replyId
     * @param array $slots
     * @param array $shellNames
     * @return static
     */
    public function react(
        string $replyId,
        array $slots = [],
        array $shellNames = ['*']
    ) : Speaker;

    /**
     * 发送一个同步的响应消息, 给当前的 Shell
     *
     * @param ReactionMsg|ConvoMsg|DirectiveMsg|Message $message
     * @return static
     */
    public function reply(Message $message) : Speaker;

    /**
     * 广播消息, 给所有的 shell
     *
     * @param Message $message
     * @return static
     */
    public function broadcast(Message $message) : Speaker;

    /**
     * 投递消息到指定的 Chat 和指定的 shell
     *
     * @param string $chatId
     * @param Message $message
     * @param array $shellNames
     * @return static
     */
    public function deliver(
        string $chatId,
        Message $message,
        array $shellNames = ['*']
    ) : Speaker;



}