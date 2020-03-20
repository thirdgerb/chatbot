<?php


namespace Commune\Chatbot\OOHost\NLU\Options;


use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Contracts\CorpusOption;
use Commune\Chatbot\OOHost\NLU\Corpus\IntExample;
use Commune\Support\Option;
use Commune\Support\Utils\StringUtils;

/**
 * @property-read string $name 意图名称
 * @property-read string $desc 介绍
 * @property-read string[] $examples 意图的例句
 * @property-read string[] $entityNames 意图定义的entities
 * @property-read string[] $keywords 意图的关键字, 如果定义的话.
 */
class IntentCorpusOption extends Option implements CorpusOption
{
    const IDENTITY = 'name';

    protected $intExamples;

    protected $intEntityNames;

    public static function stub(): array
    {
        return [
            'name' => '',
            'desc' => '',
            'examples' => [],
            'entityNames' => [],
            'keywords' => [],
        ];
    }
    protected function init(array $data): array
    {
        $data['name'] = StringUtils::normalizeContextName($data['name']);
        return parent::init($data);
    }


    /**
     * @param Corpus $corpus
     * @return EntityDictOption[]
     */
    public function getEntityDictOptions(Corpus $corpus) :array
    {
        $names = $this->entityNames;
        if (empty($names)) {
            return [];
        }

        $result = [];
        foreach ($names as $name) {
            if ($corpus->entityDictManager()->has($name)) {
                $result[$name] = $corpus->entityDictManager()->get($name);
            }
        }
        return $result;
    }



    public function mergeEntityNames(array $entityNames) : void
    {
        $this->data['entityNames'] = array_unique(array_merge($this->data['entityNames'], $entityNames));

    }

    public function setKeywords(array $keywords) : void
    {
        $this->data['keywords'] = $keywords;
    }

    public function setDesc(string $desc, bool $force = false) : void
    {
        $selfDesc = $this->data['desc'] ?? null;

        if (!empty($selfDesc) && !$force) {
            return;
        }
        $this->data['desc'] = $desc;
    }

    public function mergeExamples(array $examples, bool $onlyEmpty = false) : void
    {
        if ($onlyEmpty && !empty($this->examples)) {
            return;
        }

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