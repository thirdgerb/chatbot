<?php


namespace Commune\Chatbot\OOHost\NLU\Contracts;


use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;

/**
 * 语料库. 用于标注意图.
 * 管理 example 例句和 entity 词典.
 */
interface Corpus
{

    /**
     * 同步所有的 corpus 配置 (有的在存储介质里, 有的是别的组件加载的)
     * 存储到公共的 corpus 存储介质里. ( OptionRepository )
     */
    public function sync() : void;

    /*-------- intent option --------*/

    /**
     * 不存在也会自动生成一个.
     * @param string $intentName
     * @return IntentCorpusOption
     */
    public function getIntentCorpus(string $intentName) : IntentCorpusOption;

    /**
     * 计算所有的 intent 数量.
     * @return int
     */
    public function countIntentCorpus() : int;

    /**
     * 是否存在
     * @param string $intentName
     * @return bool
     */
    public function hasIntentCorpus(string $intentName) : bool;

    /**
     * 删除一个已存储的 intentOption
     * @param string $intentName
     */
    public function removeIntentCorpus(string $intentName) : void;

    /**
     * 迭代所有的 intentOption
     * @return IntentCorpusOption[]
     */
    public function eachIntentCorpus() : \Generator;

    /**
     * @param string[] $intentNames
     * @return IntentCorpusOption[]
     */
    public function getIntentCorpusMap(array $intentNames) : array;

    /**
     * 保存改动, 或者创建新的 intentCorpus
     * @param IntentCorpusOption $option
     */
    public function saveIntentCorpus(IntentCorpusOption $option) : void;

    /*-------- entity option --------*/

    /**
     * @param string $entityName
     * @return bool
     */
    public function hasEntityDict(string $entityName) : bool;

    /**
     * @param string $entityName
     * @return EntityDictOption
     */
    public function getEntityDict(string $entityName) : EntityDictOption;

    /**
     * @return \Generator
     */
    public function eachEntityDict() : \Generator;

    /**
     * @param string $entityName
     */
    public function removeEntityDict(string $entityName) : void;

    /**
     * @param array $entityNames
     * @return array
     */
    public function getEntityDictMap(array $entityNames) : array;


    public function saveEntityDict(EntityDictOption $option) : void;
}