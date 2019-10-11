<?php


namespace Commune\Chatbot\OOHost\NLU\Options;


use Commune\Support\Option;

/**
 * @property-read string $name entity name
 * @property-read string $desc 介绍
 * @property-read string[] $values entity 词典里的值
 * @property-read SynonymOption[] $synonyms entity下有同义词的值.
 */
class EntityDictOption extends Option
{
    const IDENTITY = 'name';

    protected static $associations = [
        'synonyms[]' => SynonymOption::class
    ];

    public static function stub(): array
    {
        return [
            'name' => '',
            'desc' => '',
            'values' => [],
            'synonyms' => [],
        ];
    }

    public function getBrief(): string
    {
        return $this->desc;
    }

}