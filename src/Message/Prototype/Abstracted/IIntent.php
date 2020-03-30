<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype\Abstracted;

use Commune\Message\Blueprint\Abstracted\Intent;
use Commune\Support\Arr\ArrayAbleToJson;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IIntent implements Intent
{
    use ArrayAbleToJson;

    /**
     * @var string
     */
    public $matchedIntentName;

    /**
     * @var array
     */
    public $possibleIntents = [];

    /**
     * @var array
     */
    public $publicEntities = [];

    /**
     * @var array
     */
    public $intentEntities = [];

    protected $sorted = false;

    public function toArray(): array
    {
        return [
            'matchedIntentName' => $this->matchedIntentName,
            'possibleIntents' => $this->possibleIntents,
            'publicEntities' => $this->publicEntities,
            'intentEntities' => $this->intentEntities,
        ];
    }

    public function getMatchedIntent(): ? string
    {
        return $this->matchedIntentName ?? $this->getMostPossibleIntent();
    }

    public function setMatchedIntent(string $intentName): void
    {
        $intentName = StringUtils::normalizeIntentName($intentName);
        $this->matchedIntentName = $intentName;
        if (! $this->hasPossibleIntent($intentName)) {
            $this->addPossibleIntent($intentName, 999);
        }
    }

    public function getMostPossibleIntent(): ? string
    {
        return $this->getPossibleIntentNames()[0] ?? null;
    }

    public function addPossibleIntent(
        string $intentName,
        int $odd,
        bool $highlyPossible = true
    )
    {
        $intentName = StringUtils::normalizeIntentName($intentName);
        $this->possibleIntents[$intentName] = [$intentName, $odd, $highlyPossible];
        $this->sorted = false;
    }

    public function getPossibleIntentData(): array
    {
        return $this->possibleIntents;
    }

    public function hasPossibleIntent(string $intentName, bool $highlyOnly = true): bool
    {
        if (!isset($this->possibleIntents[$intentName])) {
            return false;
        }

        if (!$highlyOnly) {
            return true;
        }

        return $this->possibleIntents[$intentName][2];
    }

    public function getPossibleIntentNames(bool $highlyOnly = true): array
    {
        if (empty($this->possibleIntents)) {
            return [];
        }

        if (!$this->sorted) {
            usort($this->possibleIntents, function ($item1, $item2){
                $odd1 = $item1[1];
                $odd2 = $item2[1];
                return $odd2 - $odd1;
            });
            $this->sorted = true;
        }

        $result = [];
        foreach ($this->possibleIntents as $name => list($intentName, $odd, $highlyPossible)) {
            if (!$highlyOnly || $highlyPossible) {
                $result[] = $intentName;
            }
        }
        return $result;
    }

    public function getOddOfPossibleIntent(string $intentName): ? int
    {
        return $this->possibleIntents[$intentName][1] ?? null;
    }

    public function setPublicEntities(array $publicEntities): void
    {
        $this->publicEntities = $publicEntities;
    }

    public function getPublicEntities(): array
    {
        return $this->publicEntities;
    }


    public function setIntentEntities(string $intentName, array $entities): void
    {
        $this->intentEntities[$intentName] = $entities;
    }


    public function getMatchedEntities(): array
    {
        $intent = $this->getMatchedIntent();
        if (empty($intent)) {
            return [];
        }
        return $this->getIntentEntities($intent);
    }

    public function getIntentEntities(string $intentName): array
    {
        $entities = $this->intentEntities[$intentName] ?? [];
        return $entities + $this->publicEntities;
    }


}