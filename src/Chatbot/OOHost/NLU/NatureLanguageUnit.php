<?php


namespace Commune\Chatbot\OOHost\NLU;


use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Intent\Registrar;

interface NatureLanguageUnit
{

    /**
     * 可以被NLU 单元处理的消息.通常是文本消息.
     * @param Message $message
     * @return bool
     */
    public function messageCouldHandle(Message $message) : bool;

    /**
     * 尝试匹配一个消息. 所得的结果应该为 matchedIntent 对象构成的数组.
     * @param Message $message
     * @return MatchedIntent[]
     */
    public function matchIntents(Message $message) : array;

    /**
     * 记录没有确定匹配到意图的消息.
     * 这其中又分为两种情况.
     * 一种是预测了意图 ( possibleIntent ), 但上下文中没有用到
     * 另一种是连预测的意图都没有.
     * 通常只有第二种情况有记录的价值, 记录的时候应该区分两种.
     *
     * @param IncomingMessage $message
     */
    public function logUnmatchedMessage(IncomingMessage $message) : void;

    /**
     * 输出意图的例句. 输出为什么格式的文件不做考虑.
     * @param Registrar $registrar
     */
    public function outputIntentExamples(Registrar $registrar) : void;
}