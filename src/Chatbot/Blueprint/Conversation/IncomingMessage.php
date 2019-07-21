<?php
/**
 * Created by PhpStorm.
 * User: BrightRed
 * Date: 2019/4/12
 * Time: 4:47 PM
 */

namespace Commune\Chatbot\Blueprint\Conversation;

use Illuminate\Support\Collection;

interface IncomingMessage extends ConversationMessage
{
    /*----- 管理可能的intent -----*/

    /**
     * @param string[] $names
     */
    public function setHighlyPossibleIntentNames(array $names) : void;

    /**
     * 增加一个可能的意图
     * @param string $intentName
     * @param Collection $entities
     * @param int $odd 计算的方法不管. 越大越优先.
     */
    public function addPossibleIntent(
        string $intentName,
        Collection $entities,
        int $odd = 0
    ) : void;

    /**
     * 判断是否存在一个可能的意图.
     * @param string $intentName
     * @return bool
     */
    public function hasHighlyPossibleIntent(string $intentName) : bool ;

    /**
     * 概率由大到小排序.
     * @return array
     */
    public function getHighlyPossibleIntentNames() : array;

    /**
     * @param string $intentName
     * @return array
     */
    public function getPossibleIntentEntities(string $intentName) : array;

    /**
     * 获取优先级最高的意图名称.
     * @return null|string
     */
    public function getMostPossibleIntent() : ? string;

    /**
     * 可能的意图
     * @return Collection
     */
    public function getPossibleIntentCollection() : Collection;

    /*----- 管理与intent 无关的 entity -----*/

    /**
     * @return Collection
     */
    public function getEntities() : Collection;

    /**
     * @param Collection $collection
     */
    public function setEntities(Collection $collection) : void;


    /*----- 管理分词 -----*/

    /**
     * @return Collection
     */
    public function getKeywords() : Collection;

    /**
     * @param string $keyword
     */
    public function addKeyword(string $keyword) : void;


    /**
     * @param Collection $keywords
     */
    public function setKeywords(Collection $keywords) : void;

    /*----- 管理情绪 -----*/

    /**
     * @return Collection
     */
    public function getEmotions() : Collection;

    /**
     * @param string $emotionName
     */
    public function addEmotion(string $emotionName) : void;

    /**
     * @param Collection string[] $emotions
     */
    public function setEmotions(Collection $emotions) : void;

}