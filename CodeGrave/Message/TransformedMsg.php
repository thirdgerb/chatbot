<?php


namespace Commune\Chatbot\Blueprint\Message;

/**
 * 将输入消息进行了二次封装或解析的新消息
 *
 * message transformed from origin message
 */
interface TransformedMsg extends Message
{
    public function getOriginMessage() : Message;
}