<?php
/**
 * Created by PhpStorm.
 * User: BrightRed
 * Date: 2019/4/12
 * Time: 4:47 PM
 */

namespace Commune\Chatbot\Blueprint\Conversation;

interface IncomingMessage extends ConversationMessage
{
    /*----- 管理可能的intent -----*/

    /**
     * 增加一个可能的意图
     * @param string $intentName
     * @param array $entities
     * @param int $odd 计算的方法不管. 越大越优先.
     */
    public function addPossibleIntent(
        string $intentName,
        array $entities,
        int $odd = 0
    ) : void;

    /**
     * 判断是否存在一个可能的意图.
     * @param string $intentName
     * @return bool
     */
    public function hasPossibleIntent(string $intentName) : bool ;

    public function getPossibleIntentNames() : array;

    public function getPossibleIntentEntities(string $intentName) : array;

    /**
     * 获取优先级最高的意图名称.
     * @return null|string
     */
    public function getHighlyPossibleIntent() : ? string;


}