<?php


namespace Commune\Chatbot\Framework\Conversation;


use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Message\Message;
use Illuminate\Support\Collection;

class NatureLanguageUnit implements NLU
{
    /**
     * @var string
     */
    protected $matchedIntentName;

    /**
     * @var Collection of map. intentName => list(string $name, int $odd, bool $highlyPossible)
     */
    protected $possibleIntents;

    /**
     * @var Collection of map . entityName => value
     */
    protected $entities;

    /**
     * @var Collection[]
     */
    protected $intentEntities = [];

    /**
     * @var Collection
     */
    protected $replies;

    /**
     * @var Collection;
     */
    protected $emotions;

    /**
     * @var bool
     */
    protected $sorted = false;

    /**
     * @var Collection
     */
    protected $focusIntents;

    /**
     * @var Collection
     */
    protected $words;

    /**
     * @var bool
     */
    protected $handled = false;

    public function done(): void
    {
        $this->handled = true;
    }

    public function isHandled(): bool
    {
        return $this->handled;
    }


    public function getMatchedIntent(): ? string
    {
        return $this->matchedIntentName ?? $this->getMostPossibleIntent();
    }

    public function setMatchedIntent(string $intentName): void
    {
        $this->matchedIntentName = $intentName;
    }

    public function addPossibleIntent(string $intentName, int $odd, bool $highlyPossible = true)
    {
        $this->getPossibleIntents()->put($intentName, [$intentName, $odd, $highlyPossible]);
        $this->sorted = false;
    }

    public function getPossibleIntents(): Collection
    {
        return $this->possibleIntents
            ?? $this->possibleIntents = new Collection();
    }


    public function getMostPossibleIntent(): ? string
    {
        $possibles = $this->getPossibleIntentNames();
        return  $possibles[0] ?? null;
    }

    public function hasPossibleIntent(string $intentName, bool $highlyOnly = true): bool
    {
        $exists = $this->getPossibleIntents()->has($intentName);
        if (!$exists || !$highlyOnly) {
            return $exists;
        }

        return $this->possibleIntents[$intentName][2];
    }

    public function getPossibleIntentNames(bool $highlyOnly = true): array
    {
        if (empty($this->possibleIntents)) {
            return [];
        }

        if (!$this->sorted) {
            $this->possibleIntents->sort( function ($item1, $item2){
                $odd1 = $item1[1];
                $odd2 = $item2[1];
                return $odd1 === $odd2 ? 0 : ($odd1 > $odd2 ? 1 : -1);
            });
            $this->sorted = true;
        }

        return array_map(
            // only intent name
            function($arr) {
                return $arr[0];
            },
            // filter
            $highlyOnly
                ? array_filter($this->possibleIntents->all(), function($arr){
                    return $arr[2];
                })
                : $this->possibleIntents->all()
        );
    }


    public function getOddOfPossibleIntent(string $intentName): ? int
    {
        return $this->possibleIntents[$intentName][1] ?? null;
    }

    public function setEntities(array $entities): void
    {
        $this->entities = new Collection($entities);
    }

    public function setIntentEntities(string $intentName, array $entities): void
    {
        $this->intentEntities[$intentName] = new Collection($entities);
    }


    public function getEntities(): Collection
    {
        return $this->entities
            ?? $this->entities = new Collection();
    }


    public function getIntentEntities(string $intentName): Collection
    {
        return $this->intentEntities[$intentName] ?? $this->getEntities();
    }

    public function getDefaultReplies(): Collection
    {
        return $this->replies
            ?? $this->replies = new Collection();
    }

    public function addDefaultReply(Message $message): void
    {
        $this->getDefaultReplies()->add($message);
    }

    public function getEmotions(): Collection
    {
        return $this->emotions
            ?? $this->emotions = new Collection();
    }

    public function addEmotion(string $emotionName): void
    {
        $this->getEmotions()->add($emotionName);
    }

    public function setEmotions(array $emotionNames): void
    {
        $this->emotions = new Collection($emotionNames);
    }

    public function focusIntent(string $intentName): void
    {
        $this->getFocusIntents()->add($intentName);
    }

    public function getFocusIntents(): Collection
    {
        return $this->focusIntents
            ?? $this->focusIntents = new Collection();
    }

    public function setWords(array $words): void
    {
        $this->words = new Collection($words);
    }

    public function getWords(): Collection
    {
        return $this->words
            ?? $this->words = new Collection();
    }


}