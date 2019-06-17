<?php


namespace Commune\Chatbot\OOHost\Context\Memory;

use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Chatbot\OOHost\Context\Definition;


/**
 * @method MemoryDefinition get(string $contextName) : ? Definition
 */
class MemoryRegistrar extends ContextRegistrar
{
    const DEF_CLAZZ = MemoryDefinition::class;

    public function register(Definition $def, bool $force = false): bool
    {
        $bool = parent::register($def);
        if ($bool) {
            ContextRegistrar::getIns()->register($def, $force);
        }
        return $bool;
    }


}