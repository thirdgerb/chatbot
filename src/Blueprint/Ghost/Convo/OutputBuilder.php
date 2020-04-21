<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Convo;

use Commune\Protocals\HostMsg;
use Commune\Protocals\Intercom\GhostMsg;


/**
 * 输出消息的构建器.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface OutputBuilder
{
    /**
     * 指定消息发送的用户. 默认是 空字符串.
     * @param string $guestId
     * @return OutputBuilder
     */
    public function toGuest(string $guestId) : OutputBuilder;

    /**
     * 指定消息发送的 CloneId. 与当前 Clone 不同的消息不会同步发送.
     * @param string $cloneId
     * @return OutputBuilder
     */
    public function toClone(string $cloneId) : OutputBuilder;

    /**
     * 指定发送的时间.
     * @param float $sections
     * @return OutputBuilder
     */
    public function deliverAt(float $sections) : OutputBuilder;

    /**
     * 指定发送的时间在若干秒后.
     * @param float $sections
     * @return OutputBuilder
     */
    public function deliverAfter(float $sections) : OutputBuilder;

    /**
     * 指定发送的消息是一个 GhostInput, 会触发一个异步任务.
     * @return OutputBuilder
     */
    public function isInput() : OutputBuilder;

    /*------- 发送一个 ReactionMsg -------*/

    /**
     * 异常类消息, 会有额外的提示效果.
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return static
     */
    public function error($intent, array $slots = array()) : OutputBuilder;

    /**
     * 重要提示. 会被渲染
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return static
     */
    public function notice($intent, array $slots = array()) : OutputBuilder;

    /**
     * 普通的消息. 无论如何都会被渲染.
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return static
     */
    public function info($intent, array $slots = array()) : OutputBuilder;

    /**
     * 这类消息如果无法渲染, 则不会发送.
     *
     * @param string $intent
     * @param array  $slots
     *
     * @return static
     */
    public function debug($intent, array $slots = array()) : OutputBuilder;


    /*------- 生成消息 -------*/

    /**
     * 得到一个 GhostMsg 实例.
     * @param HostMsg $message
     * @return GhostMsg
     */
    public function withMessage(HostMsg $message) : GhostMsg;

}