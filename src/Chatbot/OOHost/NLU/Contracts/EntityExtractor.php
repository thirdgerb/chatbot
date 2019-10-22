<?php


namespace Commune\Chatbot\OOHost\NLU\Contracts;


/**
 * 从字符串中匹配出实体.
 */
interface EntityExtractor
{

    /**
     * @param string $text
     * @param string $entityName
     * @return string[] values
     */
    public function match(string $text, string $entityName) : array;

}