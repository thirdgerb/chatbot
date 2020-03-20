<?php


namespace Commune\Chatbot\Blueprint\Message;

/**
 * 特殊的消息.
 * 不需要回复, 会被 hearing 忽视, 除非主动响应.
 */
interface EventMsg extends ConvoMsg
{
    public function getEventName() : string;
}