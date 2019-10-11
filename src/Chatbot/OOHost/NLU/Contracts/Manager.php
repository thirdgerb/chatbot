<?php


namespace Commune\Chatbot\OOHost\NLU\Contracts;


interface Manager
{

    /**
     * 语料库
     * @return Corpus
     */
    public function getCorpus() : Corpus;

    /**
     * Entity 词库
     * 同义词
     * @return Dictionary
     */
    public function getDictionary() : Dictionary;

    /**
     * @return NLUService
     */
    public function getMatcher() : NLUService;

    /**
     * 与远端同步配置.
     */
    public function synchronize() : bool;

}