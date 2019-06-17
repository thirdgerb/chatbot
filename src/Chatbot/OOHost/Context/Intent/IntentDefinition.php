<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Chatbot\OOHost\Context\Definition;

/**
 * @method IntentMessage newContext(...$args) : Context
 */
interface IntentDefinition extends Definition
{

    public function getMatcherOption(): IntentMatcherOption;

}