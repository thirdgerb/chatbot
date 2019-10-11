<?php


namespace Commune\Chatbot\OOHost\NLU\Options;


use Commune\Chatbot\OOHost\NLU\Corpus\IntExample;
use Commune\Support\Option;

/**
 * @property-read string $intentName 意图名称
 * @property-read string $desc 介绍
 * @property-read string[] $examples 意图的例句
 * @property-read string[] $entityNames 意图定义的entities
 */
class IntentCorpusOption extends Option
{
    const IDENTITY = 'intentName';

    protected $intExamples;

    protected $intEntityNames;

    public static function stub(): array
    {
        return [
            'intentName' => '',
            'desc' => '',
            'examples' => [],
            'entityNames' => [],
        ];
    }

    public function mergeEntityNames(array $entityNames) : void
    {
        $this->data['entityNames'] = array_unique(array_merge($this->data['entityNames'], $entityNames));

    }

    public function setDesc(string $desc, bool $force = false) : void
    {
        $selfDesc = $this->data['desc'] ?? null;

        if (!empty($selfDesc) && !$force) {
            return;
        }
        $this->data['desc'] = $desc;
    }

    public function mergeExamples(array $examples) : void
    {
        $this->intEntityNames = null;
        $this->intExamples = null;
        $this->data['examples'] = array_unique(array_merge($this->data['examples'], $examples));
    }

    public function resetExamples(array $examples) : void
    {
        $this->intEntityNames = null;
        $this->intExamples = null;
        $this->data['examples'] = $examples;
    }

    public function addExample(string $example) : void
    {
        $this->intEntityNames = null;
        $this->intExamples = null;
        $examples = $this->data['examples'];
        $examples[] = $example;
        $this->data['examples'] = array_unique($examples);
    }

    /**
     * @return IntExample[]
     */
    public function getIntExamples() : array
    {
        return $this->intExamples
            ?? $this->intExamples = array_map(function(string $example){
                return new IntExample($example);
            }, $this->examples);
    }

    /**
     * @return string[]
     */
    public function getIntEntityNames() : array
    {
        if (isset($this->intEntityNames)) {
            return $this->intEntityNames;
        }

        $result = [];
        foreach ($this->getIntExamples() as $example) {
            foreach ($example->getExampleEntities() as $entity) {
                $result[] = $entity->name;
            }
        }

        return $this->intEntityNames = array_values(array_merge($result, $this->entityNames));
    }


    public function getBrief(): string
    {
        return $this->desc;
    }

}