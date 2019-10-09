<?php


namespace Commune\Chatbot\Framework\Conversation;


use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Support\Utils\StringUtils;
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
        $intentName = StringUtils::normalizeContextName($intentName);
        $this->matchedIntentName = $intentName;
        $this->addPossibleIntent($intentName, 100);
    }

    public function addPossibleIntent(string $intentName, int $odd, bool $highlyPossible = true)
    {
        $intentName = StringUtils::normalizeContextName($intentName);
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
        $intentName = StringUtils::normalizeContextName($intentName);
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
            $this->possibleIntents = $this->possibleIntents->sort( function ($item1, $item2){
                $odd1 = $item1[1];
                $odd2 = $item2[1];
                return $odd1 === $odd2
                    ? 0
                    // 越大排越前面.
                    : ($odd1 > $odd2 ? -1 : 1);
            });
            $this->sorted = true;
        }

        $result = [];
        foreach ($this->possibleIntents->all() as $name => list($intentName, $odd, $highlyPossible)) {
            if (!$highlyOnly || $highlyPossible) {
                $result[] = $intentName;
            }

        }

        return $result;
    }


    public function getOddOfPossibleIntent(string $intentName): ? int
    {
        $intentName = StringUtils::normalizeContextName($intentName);
        return $this->possibleIntents[$intentName][1] ?? null;
    }

    public function setEntities(array $entities): void
    {
        $this->entities = new Collection($entities);
    }

    public function setIntentEntities(string $intentName, array $entities): void
    {
        $intentName = StringUtils::normalizeContextName($intentName);
        $this->intentEntities[$intentName] = new Collection($entities);
    }


    public function getGlobalEntities(): Collection
    {
        return $this->entities ?? $this->entities = new Collection();
    }


    public function getIntentEntities(string $intentName): Collection
    {
        $intentName = StringUtils::normalizeContextName($intentName);
        $entities = $this->getGlobalEntities();
        if (isset($this->intentEntities[$intentName])) {
            return $entities->merge($this->intentEntities[$intentName]);
        }
        return $entities;
    }

    public function getMatchedEntities(): Collection
    {
        if (!isset($this->matchedIntentName)) {
            return $this->getGlobalEntities();
        }

        return $this->getIntentEntities($this->matchedIntentName);
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
        $intentName = StringUtils::normalizeContextName($intentName);
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