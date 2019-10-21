<?php


namespace Commune\Chatbot\OOHost\NLU\Options;


use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Contracts\CorpusOption;
use Commune\Support\Option;

/**
 * @property-read string $name entity name
 * @property-read string $desc 介绍
 * @property-read string[] $values entity 词典里的值
 * @property-read string[] $synonyms 有同义词词典的值.
 * @property-read string[] $blacklist 黑名单, 命中了就肯定不是了.
 *
 */
class EntityDictOption extends Option implements CorpusOption
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            'name' => '',
            'desc' => '',
            'values' => [],
            'blacklist' => [],
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