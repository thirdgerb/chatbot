<?php

/**
 * Class ConversationMessage
 * @package Commune\Chatbot\Framework\Conversation
 */

namespace Commune\Chatbot\Framework\Conversation;

use Commune\Chatbot\Blueprint\Conversation\IncomingMessage;

class IncomingMessageImpl extends ConversationMessageImpl implements IncomingMessage
{
    /**
     * @var array[]
     */
    protected $possibleIntents = [];


    public function addPossibleIntent(string $intentName, array $entities, int $odd = 0): void
    {
        $this->possibleIntents[$intentName] = [$entities, $odd];
    }

    public function hasPossibleIntent(string $intentName): bool
    {
        return isset($this->possibleIntents[$intentName]);
    }

    public function getPossibleIntentEntities(string $intentName): array
    {
        return $this->possibleIntents[$intentName][0] ?? [];
    }


    public function getHighlyPossibleIntent(): ? string
    {
        $order = $this->getPossibleIntentNames();
        return $order[0] ?? null;
    }

    public function getPossibleIntentNames(): array
    {
        if (empty($this->possibleIntents)) {
            return [];
        }

        $order = [];
        foreach ($this->possibleIntents as $name => list($entities, $odd)) {
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


}