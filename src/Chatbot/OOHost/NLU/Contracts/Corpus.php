<?php


namespace Commune\Chatbot\OOHost\NLU\Contracts;


use Commune\Chatbot\OOHost\NLU\Corpus\Example;

/**
 * 语料库. 用于标注意图.
 */
interface Corpus
{
    /*-------- 增, 删, 改 -------*/

    public function addExample(string $intentName, Example $example) : void;

    /**
     * @param string $intentName
     * @param Example[] $example
     */
    public function setExamples(string $intentName, array $example) : void;

    /*-------- 查 --------*/

    public function hasExample(string $intentName) : bool;

    public function getExamples(string $intentName) : array;

    /**
     * @param string[] $intentNames
     * @return Example[][]
     */
    public function getExamplesMap(array $intentNames) : array;

}