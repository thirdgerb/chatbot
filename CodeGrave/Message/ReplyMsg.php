<?php


namespace Commune\Chatbot\Blueprint\Message;


use Illuminate\Support\Collection;

/**
 * 回复消息的容器, 持有回复ID (replyId), 和渲染用到的参数 (slots)
 *
 * information container holds template id and slots
 * when conversation receive view message, will generate real messages with it by render
 */
interface ReplyMsg extends Message
{
    /**
     * 返回 reply 消息的 ID, 用于选择模板.
     *
     * id of the messages template
     * @return string
     */
    public function getReplyId() : string;

    /**
     * 获取 Slots 参数. 得到一个 Collection 实例.
     * 注意!!! Collection 实例自带方法都会生成新实例, 不会修改 ReplyMsg 原有的 Slots
     * 要修改 slots, 请用 mergeSlots
     *
     * variables of the message
     * @return Collection
     */
    public function getSlots() : Collection;

    /**
     * 增加 slots
     * merge slots to origin slots
     * @param array $slots
     */
    public function mergeSlots(array $slots) : void;

}