<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\ITools;

use Commune\Blueprint\Framework\Command\CommandDef;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Tools\Hearing;
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Framework\Spy\SpyAgency;
use Commune\Support\SoundLike\SoundLikeInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class FakeHearing implements Hearing
{
    /**
     * @var Hearing
     */
    protected $hearing;

    /**
     * FakeHearing constructor.
     * @param Hearing $hearing
     */
    public function __construct(Hearing $hearing)
    {
        $this->hearing = $hearing;
        SpyAgency::incr(static::class);
    }


    public function todo($action): Hearing
    {
        return $this;
    }

    public function then($action = null): Hearing
    {
        return $this;
    }

    public function component($action): Hearing
    {
        return $this;
    }

    public function fallback($action): Hearing
    {
        return $this;
    }


    public function end($fallbackStrategy = null): Operator
    {
        return $this->hearing->end($fallbackStrategy);
    }


    public function getMatchedParams(): array
    {
        return [];
    }

    public function truly(): bool
    {
        return false;
    }

    public function refresh(): Matcher
    {
        return $this;
    }

    public function isEvent(string $eventName): Matcher
    {
        return $this;
    }

    public function isEventIn(array $eventNames): Matcher
    {
        return $this;
    }

    public function expect($prediction): Matcher
    {
        return $this;
    }

    public function is(string $text): Matcher
    {
        return $this;
    }

    public function isEmpty(): Matcher
    {
        return $this;
    }

    public function pregMatch(string $pattern): Matcher
    {
        return $this;
    }

    public function isVerbal(): Matcher
    {
        return $this;
    }

    public function isInstanceOf(string $messageClazz): Matcher
    {
        return $this;
    }

    public function isProtocal(string $protocalName): Matcher
    {
        return $this;
    }

    public function soundLike(
        string $text,
        string $lang = SoundLikeInterface::ZH
    ): Matcher
    {
        return $this;
    }

    public function soundLikePart(
        string $text,
        string $lang = SoundLikeInterface::ZH,
        int $type = SoundLikeInterface::COMPARE_ANY_PART
    ): Matcher
    {
        return $this;
    }

    public function action($action): Hearing
    {
        return $this;
    }

    public function isAnswered(): Matcher
    {
        return $this;
    }

    public function isAnswerOf(string $answerInterface): Matcher
    {
        return $this;
    }

    public function isChoice($suggestionIndex): Matcher
    {
        return $this;
    }

    public function isCommand(string $signature, bool $correct = false): Matcher
    {
        return $this;
    }


    public function matchCommandDef(CommandDef $def, bool $corrent = false): Matcher
    {
        return $this;
    }


    public function hasKeywords(
        array $keyWords,
        array $blacklist = [],
        bool $normalize = false
    ): Matcher
    {
        return $this;
    }

    public function feels(string $emotionName): Matcher
    {
        return $this;
    }

    public function isPositive(): Matcher
    {
        return $this;
    }

    public function isNegative(): Matcher
    {
        return $this;
    }

    public function isIntent(string $intentName): Matcher
    {
        return $this;
    }

    public function isIntentIn(array $intentNames): Matcher
    {
        return $this;
    }

    public function isAnyIntent(): Matcher
    {
        return $this;
    }

    public function isIntentMsg(string ...$intentNames): Matcher
    {
        return $this;
    }


    public function hasPossibleIntent(string $intentName): Matcher
    {
        return $this;
    }

    public function hasEntity(string $entityName, bool $defExtractor = false): Matcher
    {
        return $this;
    }

    public function hasEntityValue(string $entityName, string $expect, bool $defExtractor = false): Matcher
    {
        return $this;
    }

    public function matchEntity(string $entityName): Matcher
    {
        return $this;
    }

    public function getDialog(): Dialog
    {
        return $this->hearing->getDialog();
    }

    public function matchStage(string $stageFullname): Matcher
    {
        return $this;
    }

    public function matchStageIn(array $intents): Matcher
    {
        return $this;
    }

    public function __destruct()
    {
        unset($this->hearing);
        SpyAgency::decr(static::class);
    }

}