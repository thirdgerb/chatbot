<?php


namespace Commune\Chatbot\OOHost\Context;


/**
 * to do 的方式定义 hear 的筛选规则.
 * 先确定能干什么, 然后再设置条件.
 * 在某些场景下, 能更清楚地看懂 hearing 能够响应的功能.
 *
 * use to do api to define hearing.
 * define action before conditions.
 * easier to see what actions can hearing api do.
 *
 * @see Hearing
 */
interface ToDoWhileHearingMessage
{
    /**
     * 返回到 hearing 的语境
     * @return Hearing
     */
    public function otherwise() : Hearing;

    /*----- while message be 判断 message 的条件 -----*/

    /**
     * @param callable $prediction
     * @return ToDoWhileHearingMessage
     */
    public function expect(callable $prediction) : ToDoWhileHearingMessage;

    public function isEmpty() : ToDoWhileHearingMessage;

    public function is(string $text) : ToDoWhileHearingMessage;

    public function pregMatch(
        string $pattern,
        array $keys = []
    ) : ToDoWhileHearingMessage;


    public function isCommand(string $signature) : ToDoWhileHearingMessage;

    public function hasKeywords(array $keyWords) : ToDoWhileHearingMessage;


    /*------- 问答相关 -------*/



    /**
     * 只要有answer, 不管上文有没有命中过.
     *
     */
    public function isAnswer() : ToDoWhileHearingMessage;


    public function isChoice($suggestionIndex) : ToDoWhileHearingMessage;

    public function hasChoice(array $choices) : ToDoWhileHearingMessage;


    /*------- 消息类型检查 -------*/

    public function isEvent(string $eventName) : ToDoWhileHearingMessage;

    public function isEventIn(array $eventName) : ToDoWhileHearingMessage;

    public function isTypeOf(string $messageType) : ToDoWhileHearingMessage;

    public function isInstanceOf(string $messageClazz) : ToDoWhileHearingMessage;

    /*------- nlu 相关逻辑 -------*/

    public function feels(string $emotionName) : ToDoWhileHearingMessage;

    public function isPositive() : ToDoWhileHearingMessage;

    public function isNegative() : ToDoWhileHearingMessage;

    public function isAnyIntent() : ToDoWhileHearingMessage;

    public function isIntent(string $intentName) : ToDoWhileHearingMessage;

    public function isIntentIn(array $intentNames) : ToDoWhileHearingMessage;

    public function hasEntity(string $entityName) : ToDoWhileHearingMessage;

}