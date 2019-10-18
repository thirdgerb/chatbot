<?php


namespace Commune\Chatbot\OOHost\NLU\Contracts;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * nlu 解析单元. 默认只用一个nlu单元.
 */
interface NLUService
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
     * 尝试从session 的各种数据中, 解析出正确的意图, 赋值给 session->nlu
     *
     * use nlu analyse session and get nlu result
     *
     * @param Session $session
     * @return Session
     */
    public function match(Session $session) :  Session;


    /**
     * 尝试同步语料库, 通常包括意图语料, 以及实体词典.
     *
     * @param Session $session
     * @return string
     */
    public function syncCorpus(Session $session) : string;


}