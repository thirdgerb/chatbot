<?php


namespace Commune\Chatbot\OOHost\Context\Memory;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Entities\PropertyEtt;

class MemoryBagDefinition extends MemoryDefinition
{
    /**
     * @var string[]
     */
    protected $entities = [];

    public function __construct(
        string $contextName,
        array $scopeTypes,
        string $description,
        array $entities
    )
    {
        $this->entities = $entities;
        parent::__construct(
            $contextName,
            MemoryBag::class,
            $description,
            $scopeTypes
        );
    }

    protected function registerClassStage(): void
    {
        parent::registerClassStage();

        foreach ($this->entities as $name) {
            $this->addEntity(new PropertyEtt($name));
        }
    }

    public function newContext(...$args): Context
    {
        return new MemoryBag($this->getName());
    }
}