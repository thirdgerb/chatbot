<?php


namespace Commune\Chatbot\App\Components\Configurable\Contexts;


use Commune\Chatbot\App\Components\Configurable\Configs\DomainConfig;
use Commune\Chatbot\App\Components\Configurable\Configs\IntentConfig;
use Commune\Chatbot\OOHost\Context\Entities\PropertyEtt;
use Commune\Chatbot\OOHost\Context\Intent\IntentDefinitionImpl;

class ConfigurableIntentDef extends IntentDefinitionImpl
{
    /**
     * @var IntentConfig
     */
    protected $intentConfig;

    /**
     * @var DomainConfig
     */
    protected $domain;

    public function __construct(DomainConfig $domain, IntentConfig $config)
    {
        $this->domain = $domain;
        $this->intentConfig = $config;

        $name = trim($domain->domain, '.') . '.' . trim($config->name, '.');

        parent::__construct(
            $name,
            ConfigurableIntent::class,
            $config->desc,
            $config->matcher,
            function(array $entities = []) {
                return new ConfigurableIntent($this->getName(), $entities);
            }
        );
    }

    protected function registerDepend(): void
    {
        parent::registerDepend();
        $entitiesMap = $this->domain->getEntitiesMap();
        foreach ($this->intentConfig->entities as $entityName) {
            if (!isset($entitiesMap[$entityName])) {
                continue;
            }
            $entity = $entitiesMap[$entityName];
            $this->addEntity(new PropertyEtt(
                $entity->name,
                $entity->question,
                $entity->memoryName,
                $entity->memoryKey
            ));

        }
    }

    public function getDomain() : DomainConfig
    {
        return $this->domain;
    }

    public function getIntentConfig() : IntentConfig
    {
        return $this->intentConfig;
    }

}