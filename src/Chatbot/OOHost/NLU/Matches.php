<?php


namespace Commune\Chatbot\OOHost\NLU;


use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Illuminate\Support\Collection;

class Matches
{
    /**
     * @var  MatchedIntent[]
     */
    public $matchedIntents = [];

    /**
     * @var Collection|null
     */
    public $entities = null;

    /**
     * @var Collection|null
     */
    public $keywords = null;

    /**
     * @var Collection|null
     */
    public $emotions = null;


    public function applyToIncomingMessage(IncomingMessage $message) : IncomingMessage
    {
        $message = $this->applyIntents($message);
        $message = $this->applyEntities($message);
        $message = $this->applyKeywords($message);
        return $this->applyEmotions($message);
    }

    protected function applyEmotions(IncomingMessage $message) : IncomingMessage
    {
        if (!empty($this->emotions)) {
            $message->setEmotions($this->emotions);
        }
        return $message;
    }


    protected function applyKeywords(IncomingMessage $message) : IncomingMessage
    {
        if (!empty($this->keywords)) {
            $message->setKeywords($this->keywords);
        }
        return $message;
    }

    protected function applyEntities(IncomingMessage $message) : IncomingMessage
    {
        if (!empty($this->entities)) {
            $message->setEntities($this->entities);
        }

        return $message;
    }


    protected function applyIntents(IncomingMessage $message) : IncomingMessage
    {
        if (empty($this->matchedIntents)) {
            return $message;
        }

        $highlyPossible = [];
        foreach ($this->matchedIntents as $matched) {
            $message->addPossibleIntent(
                $matched->name,
                $matched->entities,
                $matched->confidence
            );

            // 高可能的意图区别对待.
            if ($matched->highlyPossible) {
                $highlyPossible[] = $matched->name;
            }
        }

        if (!empty($highlyPossible)) {
            $message->setHighlyPossibleIntentNames($highlyPossible);
        }

        return $message;
    }
}