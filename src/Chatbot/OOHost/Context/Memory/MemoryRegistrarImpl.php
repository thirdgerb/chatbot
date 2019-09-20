<?php


namespace Commune\Chatbot\OOHost\Context\Memory;

use Commune\Chatbot\OOHost\Context\ContextRegistrarImpl;

/**
 * @method MemoryDefinition getDef(string $contextName) : ? Definition
 */
class MemoryRegistrarImpl extends ContextRegistrarImpl implements MemoryRegistrar
{
    const DEF_CLAZZ = MemoryDefinition::class;

    public function getRegistrarId(): string
    {
        return MemoryRegistrar::class;
    }
}