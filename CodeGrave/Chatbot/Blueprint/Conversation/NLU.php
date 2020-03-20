<?php


namespace Commune\Chatbot\Blueprint\Conversation;

use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Support\Arr\ArrayAndJsonAble;
use Illuminate\Support\Collection;

/**
 * 封装了请求消息的自然语言解析数据
 *
 * NLU protocol
 * to set or get nlu information of incoming message
 *
 */
interface NLU extends ArrayAndJsonAble
{

    /**
     * 标记 NLU 已经完成过信息获取. 并记录是谁获取的.
     *
     * mark nlu has be handled
     *
     * @param string|null $nluId
     */
    public function done(string $nluId = null) : void;


    /**
     * NLU的数据已经处理过.
     * 通常有多个NLU的情况下, 也只处理一次.
     * 返回处理者的ID
     *
     * @return null|string
     */
    public function isHandledBy() : ? string;

    /*----- matched intent -----*/

    /**
     * 获取已匹配到的intent
     *
     * @return null|string
     */
    public function getMatchedIntent() : ? string;

    /**
     * 设置一个匹配到的intent
     *
     * @param string $intentName
     */
    public function setMatchedIntent(string $intentName) : void;

    /*----- possible intent -----*/


    /**
     * @param string $intentName 意图名称
     * @param int $odd 置信度, 注意是整数, 可以自己设计计算办法
     * @param bool $highlyPossible 表示置信度高于阈值了.
     * @return mixed
     */
    public function addPossibleIntent(string $intentName, int $odd, bool $highlyPossible = true);

    /**
     * 获取所有的意图信息, 通常用于记录日志, 分析, 展示.
     *
     * @return Collection of list(string $intentName, int $odd, bool $highlyPossible)
     */
    public function getPossibleIntents() : Collection;

    /**
     * 将所有的 PossibleIntent 进行排序, 获取置信度最高的意图
     * @return null|string
     */
    public function getMostPossibleIntent() : ? string;

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
     * @param array $entities
     */
    public function setEntities(array $entities) : void;

    /**
     * 合并新的 Entities 到全局的 Entities 中
     *
     * 添加 entities 信息, 到 global entity
     * @param array $entities
     */
    public function mergeEntities(array $entities) : void;

    /**
     * @param string $intentName
     * @param array $entities
     */
    public function setIntentEntities(string $intentName, array $entities) : void;

    /**
     * 全局的entity. 有些 NLU 的 entity 和 intent 是分开匹配的.
     *
     * @return Collection of entities map
     */
    public function getGlobalEntities() : Collection;

    /**
     * 获取 全局entities + 命中意图的 entities
     * @return Collection of global and matched intent entities
     */
    public function getMatchedEntities() : Collection;

    /**
     * @param string $intentName
     * @return Collection
     */
    public function getIntentEntities(string $intentName) : Collection;


    /*----- extra -----*/

    /**
     * 允许从 NLU 获取默认回复, 取代系统自带的拒答服务.
     *
     * @return Message[]
     */
    public function getDefaultReplies() : array;

    /**
     * @param Message $message
     */
    public function addDefaultReply(Message $message) : void;


    /*----- emotion, 还是实验中的思路 -----*/

    /**
     * @see \Commune\Chatbot\OOHost\Emotion\Emotion
     * @return Collection
     */
    public function getEmotions() : Collection;

    /**
     * @param string $emotionName
     */
    public function addEmotion(string $emotionName) : void;

    /**
     * @param string[] $emotionNames
     */
    public function setEmotions(array $emotionNames) : void;

    /*----- words 分词 -----*/

    /**
     * @param array $words
     */
    public function setWords(array $words) : void;

    /**
     * @return Collection
     */
    public function getWords() : Collection;

    /*----- focus -----*/

    /**
     * NLU 知道当前 stage 要注意哪些 intent
     * @param string $intentName
     */
    public function focusIntent(string $intentName) : void;

    /**
     * 获取 nlu 所有注意中的intent
     * @return Collection
     */
    public function getFocusIntents() : Collection;
}