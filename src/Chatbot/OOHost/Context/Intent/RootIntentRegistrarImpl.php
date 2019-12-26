<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Contracts\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Contracts\RootIntentRegistrar;
use Commune\Chatbot\OOHost\Context\Registrar\AbsParentContextRegistrar;

/**
 * @method IntentRegistrar[] eachSubRegistrar($recursive = true) : \Generator
 */
class RootIntentRegistrarImpl extends AbsParentContextRegistrar implements RootIntentRegistrar
{
    public function getRegistrarId(): string
    {
        return RootIntentRegistrar::class;
    }

    public function matchIntent(NLU $nlu, Message $message): ? IntentMessage
    {
        foreach ($this->eachSubRegistrar(false) as $item) {
            $matched = $item->matchIntent($nlu, $message);
            if (isset($matched)) {
                return $matched;
            }
        }
        return null;
    }

    public function matchCertainIntent(
        string $intentName,
        NLU $nlu,
        Message $message
    ) : ? IntentMessage
    {
        foreach ($this->eachSubRegistrar(false) as $item) {
            $matched = $item->matchCertainIntent($intentName, $nlu, $message);
            if (isset($matched)) {
                return $matched;
            }
        }
        return null;
    }

    public function isCommandIntent(string $intentName): bool
    {
        foreach ($this->eachSubRegistrar(false) as $item) {
            if ($item->isCommandIntent($intentName)) {
                return true;
            }
        }
        return false;
    }


}