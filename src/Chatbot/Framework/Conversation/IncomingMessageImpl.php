<?php

/**
 * Class ConversationMessage
 * @package Commune\Chatbot\Framework\Conversation
 */

namespace Commune\Chatbot\Framework\Conversation;

use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;
use Illuminate\Support\Collection;

class IncomingMessageImpl extends ConversationMessageImpl implements IncomingMessage
{
    /**
     * @var array[]
     */
    protected $possibleIntents = [];

    /**
     * @var string[]
     */
    protected $highlyPossible = [];

    public function setHighlyPossibleIntentNames(array $names): void
    {
        $this->highlyPossible = $names;
    }

    public function addPossibleIntent(string $intentName, Collection $entities, int $odd = 0): void
    {
        $this->possibleIntents[$intentName] = [$entities, $odd];
    }

    public function hasPossibleIntent(string $intentName): bool
    {
        return isset($this->possibleIntents[$intentName]);
    }

    public function getPossibleIntentEntities(string $intentName): array
    {
        $collection = $this->possibleIntents[$intentName][0] ?? null;
        return $collection instanceof Collection
            ? $collection->toArray()
            : [];
    }


    public function getMostPossibleIntent() : ? string
    {
        $order = $this->getHighlyPossibleIntentNames();
        return $order[0] ?? null;
    }

    public function getHighlyPossibleIntentNames(): array
    {
        if (empty($this->possibleIntents)) {
            return [];
        }

        $order = [];

        foreach ($this->highlyPossible as $name) {
            list($entities, $odd) = $this->possibleIntents[$name] ?? [null, 0];
            $order[] = [$odd, $name];
        }

        // 按优先级进行排序.
        usort($order, function($a, $b){
            // 大的是1.
            return $a[0] < $b[0] ? 1 : ($a[0] > $b[0] ? -1 : 0);
        });

        return array_map(function($i) {
            return $i[1];
        }, $order);
    }

    public function getPossibleIntentCollection(): Collection
    {
        return new Collection(array_map(function($item){
            return $item[1];
        }, $this->possibleIntents));
    }


}