<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Tools;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Protocals\HostMsg;

/**
 * 对话模块
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Deliver
{

    /**
     * 异常类消息, 会有额外的提示效果.
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return static
     */
    public function error(string $intent, array $slots = array()) : Deliver;

    /**
     * 重要提示. 会被渲染
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return static
     */
    public function notice(string $intent, array $slots = array()) : Deliver;

    /**
     * 普通的消息. 无论如何都会被渲染.
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return static
     */
    public function info(string $intent, array $slots = array()) : Deliver;

    /**
     * 这类消息如果无法渲染, 则不会发送.
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return static
     */
    public function debug(string $intent, array $slots = array()) : Deliver;

    /**
     * 发送所有的消息, 并返回到 dialog
     * @return Dialog
     */
    public function over() : Dialog;


    /*------- 设定额外的参数 -------*/

    /**
     * 设定默认消息携带的参数
     *
     * @param array $slots
     * @return Deliver
     */
    public function withSlots(array $slots) : Deliver;

    /**
     * 指定投递到另一个 SessionId.
     * 会作为一个异步消息发送.
     *
     * @param string $sessionId
     * @param bool $isOutput      默认是一个异步的输入消息, 否则是回复消息. 会从目标 Session 投递
     * @return Deliver
     */
    public function withSessionId(string $sessionId, bool $isOutput = false) : Deliver;

    /**
     * 指定发送消息的用户信息. 默认是机器人自身.
     *
     * @param string $creatorId
     * @param string $creatorName
     * @return Deliver
     */
    public function withCreator(string $creatorId, string $creatorName = '') : Deliver;

    /**
     * 指定发送的时间.
     * @param int $timestamp
     * @return Deliver
     */
    public function deliverAt(int $timestamp) : Deliver;

    /**
     * 指定发送的时间在若干秒后.
     * @param int $sections
     * @return Deliver
     */
    public function deliverAfter(int $sections) : Deliver;


    /*------- 发送一个 ReactionMsg -------*/

    public function message(HostMsg $message) : Deliver;

    /**
     * 立刻发送消息.
     */
    public function __invoke() : void;
}