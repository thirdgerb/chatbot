<?php


namespace Commune\Chatbot\OOHost\Context\Memory;


use Commune\Chatbot\OOHost\Context\ContextDefinition;

class MemoryDefinition extends ContextDefinition
{
    const ACCEPT_CLAZZ = AbsMemory::class;

    /**
     * @var array
     */
    protected $scopeTypes;

    public function __construct(
        string $contextName,
        string $contextClazz,
        string $description,
        array $scopeTypes
    )
    {
        $this->scopeTypes = $scopeTypes;
        parent::__construct($contextName, $contextClazz, $description);
    }

    public function getScopeTypes() : array
    {
        return $this->scopeTypes;
    }
}