<?php


namespace Commune\Chatbot\OOHost\Context\Memory;

use Commune\Chatbot\OOHost\Context\Contracts\RootMemoryRegistrar;
use Commune\Chatbot\OOHost\Context\Registrar\AbsParentContextRegistrar;

/**
 * @method MemoryDefinition getDef(string $contextName) : ? Definition
 */
class RootMemoryRegistrarImpl extends AbsParentContextRegistrar implements RootMemoryRegistrar
{
    const DEF_CLAZZ = MemoryDefinition::class;

    public function getRegistrarId(): string
    {
        return RootMemoryRegistrar::class;
    }
}