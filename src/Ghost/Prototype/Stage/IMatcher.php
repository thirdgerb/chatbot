<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Prototype\Stage;

use Commune\Framework\Blueprint\Command\CommandMsg;
use Commune\Ghost\Blueprint\Callables\Prediction;
use Commune\Ghost\Blueprint\Stage\Matcher;
use Commune\Ghost\Blueprint\Stage\Stage;
use Commune\Message\Blueprint\ArrayMsg;
use Commune\Message\Blueprint\Message;
use Commune\Support\SoundLike\SoundLikeInterface;
use Illuminate\Support\Collection;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IMatcher implements Matcher
{

    /**
     * @var Stage
     */
    protected $stage;

    /**
     * @var Message
     */
    protected $message;

    /**
     * IMatcher constructor.
     * @param Stage $stage
     * @param Message $message
     */
    public function __construct(Stage $stage, Message $message)
    {
        $this->stage = $stage;
        $this->message = $message;
    }


    public function isEvent(string $eventName): bool
    {
        // TODO: Implement isEvent() method.
    }

    public function isEventIn(array $eventName): bool
    {
        // TODO: Implement isEventIn() method.
    }

    public function expect(callable $prediction): bool
    {
        // TODO: Implement expect() method.
    }

    public function is(string $text): bool
    {
        // TODO: Implement is() method.
    }

    public function isEmpty(): bool
    {
        // TODO: Implement isEmpty() method.
    }

    public function pregMatch(string $pattern, array $keys = []): ArrayMsg
    {
        // TODO: Implement pregMatch() method.
    }

    public function isVerbal(): bool
    {
        // TODO: Implement isVerbal() method.
    }

    public function isInstanceOf(string $messageClazz): bool
    {
        // TODO: Implement isInstanceOf() method.
    }

    public function soundLike(
        string $text,
        string $lang = SoundLikeInterface::ZH
    ): bool
    {
        // TODO: Implement soundLike() method.
    }

    public function soundLikePart(
        string $text,
        int $type = SoundLikeInterface::COMPARE_ANY_PART,
        string $lang = SoundLikeInterface::ZH
    ): bool
    {
        // TODO: Implement soundLikePart() method.
    }

    public function matchEntity(
        string $entityName,
        callable $action = null
    ): ? Collection
    {
        // TODO: Implement matchEntity() method.
    }

    public function isAnswer(string $answer): bool
    {
        // TODO: Implement isAnswer() method.
    }

    public function isChoice($suggestionIndex): bool
    {
        // TODO: Implement isChoice() method.
    }

    public function hasChoiceIn(array $choices): bool
    {
        // TODO: Implement hasChoiceIn() method.
    }

    public function isCommand(string $signature): ? CommandMsg
    {
        // TODO: Implement isCommand() method.
    }

    public function hasKeywords(array $keyWords): ? Collection
    {
        // TODO: Implement hasKeywords() method.
    }

    public function feels(string $emotionName): bool
    {
        // TODO: Implement feels() method.
    }

    public function isPositive(): bool
    {
        // TODO: Implement isPositive() method.
    }

    public function isNegative(): bool
    {
        // TODO: Implement isNegative() method.
    }

    public function isAnyIntent(): bool
    {
        // TODO: Implement isAnyIntent() method.
    }

    public function isIntent(string $intentName): bool
    {
        // TODO: Implement isIntent() method.
    }

    public function isIntentIn(array $intentNames): bool
    {
        // TODO: Implement isIntentIn() method.
    }

    public function hasPossibleIntent(string $intentName): bool
    {
        // TODO: Implement hasPossibleIntent() method.
    }

    public function hasEntity(string $entityName): bool
    {
        // TODO: Implement hasEntity() method.
    }

    public function hasEntityValue(string $entityName, $expect): bool
    {
        // TODO: Implement hasEntityValue() method.
    }

    public function onHelp(string $mark = '?'): bool
    {
        // TODO: Implement onHelp() method.
    }


}