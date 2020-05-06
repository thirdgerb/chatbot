<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost;

/**
 * 对话模块
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Typer
{

    /*------- 设定额外的参数 -------*/

    public function withSlots(array $slots) : Typer;

    /**
     * 指定消息发送的用户. 默认是 空字符串.
     * @param string $guestId
     * @return Typer
     */
    public function toGuest(string $guestId) : Typer;

    /**
     * 指定消息发送的 CloneId. 与当前 Clone 不同的消息不会同步发送.
     * @param string $cloneId
     * @return Typer
     */
    public function toClone(string $cloneId) : Typer;

    /**
     * 指定消息发送的目标 Shell
     * @param string $shellName
     * @return Typer
     */
    public function toShell(string $shellName) : Typer;

    /**
     * 指定发送的时间.
     * @param float $sections
     * @return Typer
     */
    public function deliverAt(float $sections) : Typer;

    /**
     * 指定发送的时间在若干秒后.
     * @param float $sections
     * @return Typer
     */
    public function deliverAfter(float $sections) : Typer;


    /*------- 发送一个 ReactionMsg -------*/

    /**
     * 异常类消息, 会有额外的提示效果.
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return static
     */
    public function error($intent, array $slots = array()) : Typer;

    /**
     * 重要提示. 会被渲染
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return static
     */
    public function notice($intent, array $slots = array()) : Typer;

    /**
     * 普通的消息. 无论如何都会被渲染.
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return static
     */
    public function info($intent, array $slots = array()) : Typer;

    /**
     * 这类消息如果无法渲染, 则不会发送.
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return static
     */
    public function debug($intent, array $slots = array()) : Typer;

    /**
     * 回到 Dialog
     * @return Dialog
     */
    public function fin() : Dialog;
}