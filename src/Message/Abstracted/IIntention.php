<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Abstracted;

use Commune\Protocals\Abstracted\Intention;
use Commune\Support\Message\AbsMessage;
use Commune\Support\Utils\StringUtils;

/**
 * 意图的理解. 可以来自 NLU 或者其它的解析策略.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string|null $matchedIntent              已经命中的意图
 * @property array $possibleIntents                          可能的意图
 * [string $intentName, float $odd, bool $highlyPossible]
 *
 * @property array[] $publicEntities                 公共的实体
 * [ string $entityName => string[] ] $entityValues
 *
 * @property array[][] $intentEntities               意图对应的实体
 */
class IIntention extends AbsMessage implements Intention
{
    protected $sorted = false;

    public static function stub(): array
    {
        return [
            'matchedIntent' => null,
            'possibleIntents' => [],
            'publicEntities' => [],
            'intentEntities' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function isEmpty(): bool
    {
        return empty($this->_data['matchedIntent'])
            && empty($this->_data['possibleIntents'])
            && empty($this->_data['publicEntities'])
            && empty($this->_data['intentEntities']);
    }

    /*----- do match -----*/

    public function matchAnyIntent(array $intents): ? string
    {
        $possible = $this->getPossibleIntentNames();
        if (empty($possible)) {
            return null;
        }

        foreach ($intents as $intent) {
            if (StringUtils::isWildCardPattern($intent)) {
                $pattern = StringUtils::wildcardToRegex($intent);
                foreach ($possible as $maybe) {
                    if (preg_match($pattern, $maybe)) {
                        return $maybe;
                    }
                }

            } elseif($this->hasPossibleIntent($intent)) {
                return $intent;
            }
        }

        return null;
    }


    public function isWildcardIntent(string $intent): bool
    {
        return StringUtils::isWildCardPattern($intent);
    }

    public function wildcardIntentMatch(string $intentPattern): ? array
    {
        $pattern = StringUtils::wildcardToRegex($intentPattern);
        $intents = $this->getPossibleIntentNames(true);

        if (!empty($intents)) {
            $matched = array_filter($intents, function($name) use ($pattern){
                $matched = preg_match($pattern, $name);
                return $matched > 0;
            });

            return empty($matched) ? null : $matched;
        }

        return null;
    }

    public function exactIntentMatch(string $intentName): bool
    {
        return $this->hasPossibleIntent($intentName);
    }


    public function getMatchedIntent(): ? string
    {
        return $this->_data['matchedIntent'] ?? $this->getMostPossibleIntent();
    }

    public function setMatchedIntent(string $intentName): void
    {
        $intentName = StringUtils::normalizeIntentName($intentName);
        $this->_data['matchedIntent'] = $intentName;
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
        $this->_data['possibleIntents'][$intentName] = [$intentName, $odd, $highlyPossible];
        $this->sorted = false;
    }

    public function getPossibleIntentData(): array
    {
        return $this->_data['possibleIntents'];
    }

    public function hasPossibleIntent(string $intentName, bool $highlyOnly = true): bool
    {
        if (!isset($this->_data['possibleIntents'][$intentName])) {
            return false;
        }

        if (!$highlyOnly) {
            return true;
        }

        return $this->_data['possibleIntents'][$intentName][2];
    }

    public function getPossibleIntentNames(bool $highlyOnly = true): array
    {
        $possibleIntents = $this->_data['possibleIntents'];
        if (empty($possibleIntents)) {
            return [];
        }

        if (!$this->sorted) {
            usort($possibleIntents, function ($item1, $item2){
                $odd1 = $item1[1];
                $odd2 = $item2[1];
                return $odd2 - $odd1;
            });
            $this->sorted = true;
            $this->_data['possibleIntents'] = $possibleIntents;
        }

        $result = [];
        foreach ($possibleIntents as $name => list($intentName, $odd, $highlyPossible)) {
            if (!$highlyOnly || $highlyPossible) {
                $result[] = $intentName;
            }
        }
        return $result;
    }

    public function getOddOfPossibleIntent(string $intentName): ? int
    {
        return $this->_data['possibleIntents'][$intentName][1] ?? null;
    }

    public function setPublicEntities(array $publicEntities): void
    {
        $this->_data['publicEntities'] = $publicEntities;
    }

    public function getPublicEntities(): array
    {
        return $this->_data['publicEntities'];
    }


    public function setIntentEntities(string $intentName, array $entities): void
    {
        $this->_data['intentEntities'][$intentName] = $entities;
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
        $entities = $this->_data['intentEntities'][$intentName] ?? [];
        return $entities + $this->_data['publicEntities'] ?? [];
    }

}