<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Messenger;

use Commune\Protocals\Intercom\GhostMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Condition
{

    /**
     * 获取指定 CloneId 的消息
     * @param string $cloneId
     * @return Condition
     */
    public function cloneIdIs(string $cloneId) : Condition;

    /**
     * 获取指定 SessionId 的消息
     * @param string $sessionId
     * @return Condition
     */
    public function sessionIdIs(string $sessionId) : Condition;

    /**
     * 获取指定批次的消息, 包括输入和输出消息.
     * @param string $batchId
     * @return Condition
     */
    public function batchIdIs(string $batchId) : Condition;

    /**
     * 获取指定用户的消息.
     * @param string $guestId
     * @return Condition
     */
    public function guestIdIs(string $guestId) : Condition;

    /**
     * 获取发送时间 T 之后需要发送的消息
     * @param float $time
     * @return Condition
     */
    public function deliverAfter(float $time) : Condition;

    /**
     * 获取创建时间 T 之后的消息
     * @param float $time
     * @return Condition
     */
    public function createdAfter(float $time) : Condition;

    /**
     * 消息 Id 大于...
     * @param string $messageId
     * @return Condition
     */
    public function afterId(string $messageId) : Condition;

    /*----- 读取消息 ------*/

    /**
     * 获取若干条消息
     * @return GhostMsg[]
     */
    public function get() : array;

    /**
     * 获取第一条消息
     * @return GhostMsg
     */
    public function first() : GhostMsg;

    /**
     * 计算消息的数量.
     * @return int
     */
    public function count() : int;

    /**
     * 获取一个区间的消息.
     * @param int $offset
     * @param int $limit
     * @return GhostMsg[]
     */
    public function range(int $offset, int $limit) : array;
}