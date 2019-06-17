<?php


namespace Commune\Demo\App\Memories;


use Commune\Chatbot\App\Memories\MemoryDef;
use Commune\Chatbot\OOHost\Session\Scope;

/**
 * @property int $test
 * @property int $test1
 */
class Sandbox extends MemoryDef
{
    const DESCRIPTION = '';

    const SCOPE_TYPES = [Scope::SESSION_ID];

}