<?php


namespace Commune\Chatbot\OOHost\NLU\Predefined;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\NLU\Contracts\Corpus;
use Commune\Chatbot\OOHost\NLU\Contracts\NLUService;
use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * 假装有个NLU
 */
class FakeNLUService implements NLUService
{
    public function messageCouldHandle(Message $message): bool
    {
        return false;
    }

    public function match(Session $session): Session
    {
        return $session;
    }

    public function syncCorpus(Corpus $corpus): string
    {
        return static::class;
    }

    public function syncIntentOption(IntentCorpusOption $option): string
    {
        return static::class;
    }

    public function syncEntityDict(EntityDictOption $option): string
    {
        return static::class;
    }


}