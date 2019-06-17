<?php


namespace Commune\Chatbot\OOHost\Context\Intent;

use Commune\Chatbot\OOHost\Context\ContextDefinition;

/**
 * @method IntentMessage newContext(...$args)
 */
class IntentDefinitionImpl extends ContextDefinition implements IntentDefinition
{
    /**
     * @var IntentMatcherOption
     */
    protected $intentOption;

    /**
     * IntentDefinitionImpl constructor.
     * @param string $name
     * @param string $intentClazz
     * @param string $desc
     * @param IntentMatcherOption $intentOption
     * @param \Closure|null $contextMaker
     */
    public function __construct(
        string $name,
        string $intentClazz,
        string $desc,
        IntentMatcherOption $intentOption,
        \Closure $contextMaker = null
    )
    {
        $this->intentOption = $intentOption;
        parent::__construct(
            $name,
            $intentClazz,
            $desc,
            $contextMaker
        );
    }

    public function getMatcherOption() : IntentMatcherOption
    {
        return $this->intentOption;
    }

}