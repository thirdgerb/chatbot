<?php


namespace Commune\Chatbot\OOHost\Dialogue\Hearing;


use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Support\SoundLike\SoundLikeInterface;

class TodoImpl implements ToDoWhileHearing
{
    /**
     * @var Hearing
     */
    protected $hearing;

    /**
     * @var callable
     */
    protected $action;

    public function __construct(Hearing $hearing, callable $action)
    {
        $this->hearing = $hearing;
        $this->action = $action;
    }

    public function otherwise(): Hearing
    {
        return $this->hearing->then($this->action);
    }

    public function todo(callable $action): ToDoWhileHearing
    {
        return $this->otherwise()->todo($action);
    }

    public function end(callable $fallback = null): Navigator
    {
        return $this->otherwise()->end($fallback);
    }

    public function isEvent(string $eventName, callable $action = null): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function isEventIn(array $eventName, callable $action = null): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function expect(
        callable $prediction,
        callable $action = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function is(
        string $text,
        callable $action = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function isEmpty(
        callable $action = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function pregMatch(
        string $pattern,
        array $keys = [],
        callable $action = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function feels(
        string $emotionName,
        callable $action = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function isPositive(callable $action = null): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function isNegative(callable $action = null): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function isAnyIntent(
        callable $intentAction = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function isIntent(
        string $intentName,
        callable $intentAction = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function isIntentIn(
        array $intentNames,
        callable $intentAction = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function hasEntity(
        string $entityName,
        callable $interceptor = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function hasEntityValue(
        string $entityName,
        $expect,
        callable $interceptor = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function isInstanceOf(
        string $messageClazz,
        callable $action = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function isVerbal(
        callable $action = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }


    public function isAnswer(callable $action = null): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function isChoice(
        $suggestionIndex,
        callable $action = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function hasChoice(
        array $choices,
        callable $action = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function isCommand(
        string $signature,
        callable $action = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function hasKeywords(
        array $keyWords,
        callable $action = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function isPreparedIntent(
        string $intentName,
        callable $whenPrepared = null,
        callable $whenNotPrepared = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function matchQuestion(Question $question): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function soundLike(
        string $text,
        callable $action = null,
        string $lang = SoundLikeInterface::ZH
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function soundLikePart(
        string $text,
        int $type = SoundLikeInterface::COMPARE_ANY_PART,
        callable $action = null,
        string $lang = SoundLikeInterface::ZH
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function onHelp(callable $helping = null, string $mark = null): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function matchEntity(
        string $entityName,
        callable $action = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }

    public function hasPossibleIntent(
        string $intentName,
        callable $intentAction = null
    ): Matcher
    {
        call_user_func_array([$this->hearing, __FUNCTION__], func_get_args());
        return $this;
    }


}