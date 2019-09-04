<?php


namespace Commune\Chatbot\OOHost\NLU\Contracts;


use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrar;

interface Manager
{
    public function getIntentRegistrar() : IntentRegistrar;

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
     * @return Matcher
     */
    public function getMatcher() : Matcher;


    /**
     * 与远端同步配置.
     */
    public function synchronize() : bool;

}