<?php


namespace Commune\Chatbot\Blueprint\Conversation;


/**
 * 会话所处的通道.
 */
interface Chat
{
    /**
     * 通道用户方的ID
     * @return string
     */
    public function getUserId() : string;

    /**
     * 通道机器人方的名字.
     * @return string
     */
    public function getChatbotName() : string;

    /**
     * 通道所处的平台id
     * @return string
     */
    public function getPlatformId() : string;

    /**
     * 通道的唯一ID.
     * @return string
     */
    public function getChatId() : string;

    /**
     * 锁定一个通道
     * 允许系统在特殊的情况下, 手动锁通道.
     *
     * @param int|null $ttl 锁过期的时间. 防止死锁.
     * @return bool
     */
    public function lock(int $ttl = null) : bool;

    /**
     * 解锁一个通道.
     */
    public function unlock() : void;

}