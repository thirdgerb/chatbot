<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts\Messenger;

use Commune\Protocals\IntercomMsg;

/**
 * MessageDB 的复杂查询逻辑
 * Condition 的定义仍然是有限的. 如果需要无限的查询能力, 就不适合用 MessageDB 模块了.
 * 因为 MessageDB 只是服务于异构项目的.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Condition
{

    /**
     * 获取指定 SessionId 的消息
     * @param string $sessionId
     * @return Condition
     */
    public function sessionIs(string $sessionId) : Condition;

    /**
     * 获取指定批次的消息, 包括输入和输出消息.
     * @param string $batchId
     * @return Condition
     */
    public function batchIs(string $batchId) : Condition;

    /**
     * 获取指定用户的消息.
     * @param string $creatorId
     * @return Condition
     */
    public function creatorIs(string $creatorId) : Condition;

    /**
     * 获取发送时间 T 之后需要发送的消息
     * @param int $time
     * @return Condition
     */
    public function deliverableAfter(int $time) : Condition;

    /**
     * 获取创建时间 T 之后的消息
     * @param int $time
     * @return Condition
     */
    public function createdAfter(int $time) : Condition;

    /**
     * 消息 Id 大于...
     * @param string $messageId
     * @return Condition
     */
    public function afterId(string $messageId) : Condition;

    /*----- 读取消息 ------*/

    /**
     * 获取若干条消息
     * @return IntercomMsg[]
     */
    public function get() : array;

    /**
     * 获取第一条消息
     * @return IntercomMsg
     */
    public function first() : ? IntercomMsg;

    /**
     * 计算消息的数量.
     * @return int
     */
    public function count() : int;

    /**
     * 获取一个区间的消息.
     * @param int $offset
     * @param int $limit
     * @return IntercomMsg[]
     */
    public function range(int $offset, int $limit) : array;
}