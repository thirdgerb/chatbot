<?php


namespace Commune\Chatbot\OOHost\NLU\Options;


use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Support\Option;

/**
 * @property-read string $name entity name
 * @property-read string $desc 介绍
 * @property-read string[] $values entity 词典里的值
 * @property-read string[] $synonyms value 里的同义词词典.
 *
 */
class EntityDictOption extends Option
{
    const IDENTITY = 'name';

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

    public function getSynonymOptions(Corpus $corpus) : array
    {
        $synonyms = $this->synonyms;
        $synonyms = empty($synonyms) ? $this->values : $synonyms;

        $result = [];

        $manager = $corpus->synonymsManager();
        foreach ($synonyms as $name) {
            if ($manager->has($name)) {
                $result[$name] = $manager->get($name);
            }
        }

        return $result;
    }

}