<?php


namespace Commune\Chatbot\OOHost\NLU\Libraries;


use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Contracts\EntityExtractor;
use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;
use Commune\Support\WordSearch\Tree;

class PHPEntityExtractor implements EntityExtractor
{
    /**
     * @var Corpus
     */
    protected $corpus;

    /**
     * @var Tree[]
     */
    protected $matchers = [];

    /**
     * PHPEntityExtractor constructor.
     * @param Corpus $corpus
     */
    public function __construct(Corpus $corpus)
    {
        $this->corpus = $corpus;
    }

    public function match(string $text, string $entityName): array
    {
        if (isset($this->matchers[$entityName])) {
            $matches = $this->matchers[$entityName]->search($text);
            return array_keys($matches);
        }

        // 没有配置要提前返回.
        if (!$this->corpus->entityDictManager()->has($entityName)) {
            return [];
        }

        $matcher = $this->buildMatcher($entityName);
        $this->matchers[$entityName] = $matcher;

        $matches = $matcher->search($text);
        return array_keys($matches);
    }

    protected function buildMatcher(string $entityName) : Tree
    {
        /**
         * @var EntityDictOption $entityDict
         */
        $entityDict = $this->corpus->entityDictManager()->get($entityName);
        $synonyms = $entityDict->getSynonymOptions($this->corpus);

        $keywords = [];
        foreach ($entityDict->values as $value) {
            $keywords[strval($value)] = strval($value);
        }

        foreach ($synonyms as $synonym) {
            $value = $synonym->value;
            foreach ($synonym->aliases as $alias) {
                $key = strval($alias);
                $keywords[$key] = strval($value);
            }
        }

        return new Tree($keywords);
    }


}