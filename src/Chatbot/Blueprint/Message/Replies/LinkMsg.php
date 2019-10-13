<?php


namespace Commune\Chatbot\Blueprint\Message\Replies;


use Commune\Chatbot\Blueprint\Message\ReplyMsg;

/**
 * 可以被渲染成链接的特殊回复.
 */
interface LinkMsg extends ReplyMsg
{
    const REPLY_ID = '';

    public function getUrl() : string;
}