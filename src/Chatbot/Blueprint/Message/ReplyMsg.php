<?php


namespace Commune\Chatbot\Blueprint\Message;


use Illuminate\Support\Collection;

/**
 * information container holds template id and slots
 * when conversation receive view message, will generate real messages with it by render
 */
interface ReplyMsg extends VerboseMsg
{
    /**
     * 返回 reply 消息的 ID, 用于选择模板.
     *
     * id of the messages template
     * @return string
     */
    public function getReplyId() : string;

    /**
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