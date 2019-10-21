<?php


namespace Commune\Chatbot\OOHost\Context\Intent;


use Commune\Chatbot\OOHost\Context\Contracts\IntentRegistrar;
use Commune\Chatbot\OOHost\Context\Contracts\RootIntentRegistrar;
use Commune\Chatbot\OOHost\Context\Registrar\AbsParentContextRegistrar;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * @method IntentRegistrar[] eachSubRegistrar($recursive = true) : \Generator
 */
class RootIntentRegistrarImpl extends AbsParentContextRegistrar implements RootIntentRegistrar
{
    public function getRegistrarId(): string
    {
        return RootIntentRegistrar::class;
    }

    public function matchIntent(Session $session): ? IntentMessage
    {
        foreach ($this->eachSubRegistrar(false) as $item) {
            $matched = $item->matchIntent($session);
            if (isset($matched)) {
                return $matched;
            }
        }
        return null;
    }

    public function matchCertainIntent(string $intentName, Session $session): ? IntentMessage
    {
        foreach ($this->eachSubRegistrar(false) as $item) {
            $matched = $item->matchCertainIntent($intentName, $session);
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