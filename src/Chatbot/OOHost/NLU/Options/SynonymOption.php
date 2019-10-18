<?php


namespace Commune\Chatbot\OOHost\NLU\Options;


use Commune\Support\Option;

/**
 * @property-read mixed $value 同义词的值
 * @property-read string $desc 介绍
 * @property-read string[] $aliases 同义词的值
 */
class SynonymOption extends Option
{
    const IDENTITY = 'value';

    public static function stub(): array
    {
        return [
            'value' => '',
            'desc' => '',
            'aliases' => [],
        ];
    }


    public function getBrief(): string
    {
        return $this->desc;
    }

}