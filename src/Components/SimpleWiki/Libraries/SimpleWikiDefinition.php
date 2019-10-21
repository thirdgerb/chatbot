<?php


namespace Commune\Components\SimpleWiki\Libraries;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Intent\IntentDefinitionImpl;
use Commune\Chatbot\OOHost\Context\Intent\IntentMatcherOption;
use Commune\Chatbot\OOHost\NLU\Contracts\IntentDefHasCorpus;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;
use Commune\Components\SimpleWiki\Options\GroupOption;
use Commune\Components\SimpleWiki\Options\WikiOption;

class SimpleWikiDefinition extends IntentDefinitionImpl implements IntentDefHasCorpus
{
    /**
     * @var GroupOption
     */
    protected $group;

    /**
     * @var WikiOption
     */
    protected $config;

    public function __construct(GroupOption $group, WikiOption $option)
    {
        $this->group = $group;
        $this->config = $option;
        parent::__construct(
            $option->intentName,
            SimpleWikiInt::class,
            $option->description,
            new IntentMatcherOption(),
            null
        );
    }

    public function getGroupConfig() : GroupOption
    {
        return $this->group;
    }

    public function getConfig() : WikiOption
    {
        return $this->config;
    }

    public function getDesc(): string
    {
        return $this->config->description;
    }

    /**
     * create a context
     * @param array $args
     * @return SimpleWikiInt
     */
    public function newContext(...$args) : Context
    {
        return new SimpleWikiInt($this->getName());
    }

    public function getDefaultCorpus(): IntentCorpusOption
    {
        return new IntentCorpusOption([
            'name' => $this->getName(),
            'desc' => $this->getDesc(),
            'examples' => $this->getConfig()->examples,
            'entityNames' => [],
        ]);
    }


}