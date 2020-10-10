<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocols\Abstracted;

/**
 * 意图的理解. 可以来自 NLU 或者其它的解析策略.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Intention
{


    /**
     * 获取已匹配到的 intentName
     *
     * @return null|string
     */
    public function getMatchedIntent() : ? string;

    /**
     * 设置一个匹配到的 intentName
     *
     * @param string $intentName
     */
    public function setMatchedIntent(string $intentName) : void;


    /**
     * 将所有的 PossibleIntent 进行排序, 获取置信度最高的意图
     *
     * @return null|string
     */
    public function getMostPossibleIntent() : ? string;


    /*----- possible intent -----*/

    /**
     * @param string $intentName 意图名称
     * @param int $odd 置信度, 注意是整数, 可以自己设计计算办法
     * @param bool $highlyPossible 表示置信度高于阈值了.
     *
     * @return mixed
     */
    public function addPossibleIntent(
        string $intentName,
        int $odd,
        bool $highlyPossible = true
    );

    /**
     * 获取所有的意图信息, 通常用于记录日志, 分析, 展示.
     *
     * @return array
     */
    public function getPossibleIntentData() : array;

    /**
     * 判断某个意图是否存在
     *
     * @param string $intentName
     * @param bool $highlyOnly  只查找置信度高于阈值的.
     * @return bool
     */
    public function hasPossibleIntent(string $intentName, bool $highlyOnly = true) : bool;

    /**
     * 获取所有匹配到的意图名
     *
     * @param bool $highlyOnly
     * @return string[]
     */
    public function getPossibleIntentNames(bool $highlyOnly = true) : array;

    /**
     * 获取某个意图的置信度
     *
     * @param string $intentName
     * @return int|null
     */
    public function getOddOfPossibleIntent(string $intentName) : ? int;

    /*----- entities -----*/

    /**
     * 设置匹配到的 Entity, 是全局的 Entity
     *
     * @param array[] $entities
     */
    public function setPublicEntities(array $entities) : void;


    /**
     * 全局的entity. 有些 NLU 的 entity 和 intent 是分开匹配的.
     *
     * @return array[]
     */
    public function getPublicEntities() : array;


    /**
     * @param string $intentName
     * @param array[] $entities
     */
    public function setIntentEntities(string $intentName, array $entities) : void;

    /**
     * 获取 全局entities + 命中意图的 entities
     *
     * @return array[][]
     */
    public function getMatchedEntities() : array;

    /**
     * @param string $entityName
     * @return bool
     */
    public function hasEntity(string $entityName) : bool;

    /**
     * 获取某个意图的所有实体.
     * 意图必须是 highly possible intent
     *
     * @param string $intentName
     * @return array[][]
     */
    public function getIntentEntities(string $intentName) : array;


}