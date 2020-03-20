<?php


namespace Commune\Components\Story\Basic;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\ContextDefinition;
use Commune\Components\Story\Options\ScriptOption;

class ScriptDefinition extends ContextDefinition
{
    const ACCEPT_CLAZZ = AbsScriptTask::class;

    /**
     * @var ScriptOption
     */
    protected $script;

    /**
     * ScriptDefinition constructor.
     * @param ScriptOption $script
     */
    public function __construct(ScriptOption $script)
    {
        $this->script = $script;
        parent::__construct(
            $script->id,
            $script->class,
            $script->title
        );
    }

    public function getScriptOption() : ScriptOption
    {
        return $this->script;
    }

    public function newContext(...$args): Context
    {
        $class = $this->getClazz();
        return new $class($this->getName());
    }


}