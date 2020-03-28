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

use Commune\Message\Blueprint\ConvoMsg;
use Commune\Message\Blueprint\Directive\DirectiveMsg;
use Commune\Message\Blueprint\Message;
use Commune\Message\Blueprint\Reaction\ReactionMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Speaker
{

    /**
     * 发送一个响应给相关 shell
     *
     * @param ReactionMsg $message
     * @param array $shellNames
     * @return Speaker
     */
    public function react(
        ReactionMsg $message,
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