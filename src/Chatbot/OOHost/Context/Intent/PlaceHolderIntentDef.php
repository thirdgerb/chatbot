<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\PlaceholderDefinition;

class PlaceHolderIntentDef extends IntentDefinitionImpl implements PlaceholderDefinition
{
    public function __construct(string $name)
    {
        parent::__construct(
            $name,
            PlaceHolderIntent::class,
            'place holder',
            new IntentMatcherOption()
        );
    }

    /**
     * create a context
     * @param array $args
     * @return PlaceHolderIntent
     */
    public function newContext(...$args) : Context
    {
        return new PlaceHolderIntent($this->getName(), $args[0] ?? []);
    }


}