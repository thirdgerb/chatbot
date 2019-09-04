<?php


namespace Commune\Chatbot\OOHost\NLU\Contracts;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * 解析单元.
 */
interface Matcher
{
    /**
     * 可以被NLU 单元处理的消息.通常是文本消息.
     *
     * determine if message could be handle by nlu
     * @param Message $message
     * @return bool
     */
    public function messageCouldHandle(Message $message) : bool;

    /**
     * use nlu analyse session and get nlu result
     *
     * @param Session $session
     * @return Session|null
     */
    public function match(Session $session) : ? Session;



}