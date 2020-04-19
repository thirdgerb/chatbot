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

use Commune\Message\Blueprint\Message;


/**
 * 输出消息的组件.
 * 包含两部分:
 *
 * 1.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Speaker
{
    /**
     * 异常类消息, 会有额外的提示效果.
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return Speaker
     */
    public function error($intent, array $slots = array());

    /**
     * 重要提示. 会被渲染
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return Speaker
     */
    public function notice($intent, array $slots = array());

    /**
     * 普通的消息. 无论如何都会被渲染.
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return Speaker
     */
    public function info($intent, array $slots = array()) : Speaker;

    /**
     * 这类消息如果无法渲染, 则不会发送.
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return Speaker
     */
    public function debug($intent, array $slots = array());


    /**
     * 发送一个响应给相关 shell
     *
     * @param ReactionMsg $intent
     * @param array $shellNames
     * @return Speaker
     */
    public function react(
        ReactionMsg $intent,
        array $shellNames = ['*']
    ) : Speaker;

    /**
     * 发送一个同步的响应消息, 给当前的 Shell
     *
     * @param ReactionMsg|Message|DirectiveMsg|Message $intent
     * @return static
     */
    public function reply(Message $intent) : Speaker;

    /**
     * 投递消息到指定的 Chat 和指定的 shell
     *
     * @param string $chatId
     * @param Message $intent
     * @param array $shellNames
     * @return static
     */
    public function deliver(
        string $chatId,
        Message $intent,
        array $shellNames = ['*']
    ) : Speaker;



}