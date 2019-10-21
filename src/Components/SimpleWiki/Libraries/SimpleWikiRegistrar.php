<?php


namespace Commune\Components\SimpleWiki\Libraries;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Definition;
use Commune\Chatbot\OOHost\Context\Intent\IntentRegistrarImpl;
use Commune\Components\SimpleWiki\Options\WikiOption;
use Commune\Components\SimpleWiki\SimpleWikiComponent;
use Commune\Support\OptionRepo\Contracts\OptionRepository;
use Illuminate\Support\Str;

/**
 * 注册自己的 simple wiki registrar, 可以从中获取 def
 */
class SimpleWikiRegistrar extends IntentRegistrarImpl
{
    /**
     * @var OptionRepository
     */
    protected $repo;

    /**
     * @var SimpleWikiComponent
     */
    protected $config;

    public function __construct(Application $app, ContextRegistrar $parent = null)
    {
        $container = $app->getProcessContainer();
        $this->repo = $container->make(OptionRepository::class);
        $this->config = $container->make(SimpleWikiComponent::class);

        parent::__construct($app, $parent);
    }

    public function getRegistrarId(): string
    {
        return static::class;
    }

    public function selfGetDefNamesByDomain(string $domain): array
    {
        $results = [];
        foreach (
            $this->repo->getAllOptionIds(WikiOption::class) as $id
        ) {
            if (Str::startsWith($id, $domain)) {
                $results[] = $id;
            }
        }
        return $results;
    }

    public function selfHasDef(string $contextName) : bool
    {
        return $this->repo->has(WikiOption::class, $contextName);
    }

    public function selfGetDef(string $contextName) : ? Definition
    {
        if (!$this->repo->has(WikiOption::class, $contextName)) {
            return null;
        }

        /**
         * @var WikiOption $option
         */
        $option = $this->repo->find(WikiOption::class, $contextName);
        $group = $this->config->getGroupByWikiOption($option);
        return new SimpleWikiDefinition($group, $option);
    }

    public function selfCountDef() : int
    {
        return $this->repo->count(WikiOption::class);
    }

    public function selfEachDef() : \Generator
    {
        foreach ($this->repo->eachOption(WikiOption::class) as $option) {
            $group = $this->config->getGroupByWikiOption($option);
            yield new SimpleWikiDefinition($group, $option);
        }
    }

    public function selfGetDefNamesByTag(string ...$tags): array
    {
        return [];
    }

    public function selfGetPlaceholderDefNames(): array
    {
        return [];
    }
}