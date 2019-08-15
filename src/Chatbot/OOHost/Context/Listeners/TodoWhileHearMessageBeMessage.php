<?php


namespace Commune\Chatbot\OOHost\Context\Listeners;


use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Context\ToDoWhileHearingMessage;

class TodoWhileHearMessageBeMessage implements ToDoWhileHearingMessage
{
    protected $hearing;

    protected $action;

    public function __construct(Hearing $hearing, callable $action)
    {
        $this->hearing = $hearing;
        $this->action = $action;
    }

    public function otherwise(): Hearing
    {
        return $this->hearing;
    }

    public function expect(callable $prediction): ToDoWhileHearingMessage
    {
        $this->hearing->expect($prediction, $this->action);
        return $this;
    }

    public function isEmpty(): ToDoWhileHearingMessage
    {
        $this->hearing->isEmpty($this->action);
        return $this;
    }

    public function is(string $text): ToDoWhileHearingMessage
    {
        $this->hearing->is($text, $this->action);
        return $this;
    }

    public function pregMatch(
        string $pattern,
        array $keys = []
    ): ToDoWhileHearingMessage
    {
        $this->hearing->pregMatch($pattern, $keys, $this->action);
        return $this;
    }

    public function isCommand(string $signature): ToDoWhileHearingMessage
    {
        $this->hearing->isCommand($signature, $this->action);
        return $this;
    }

    public function hasKeywords(array $keyWords): ToDoWhileHearingMessage
    {
        $this->hearing->hasKeywords($keyWords, $this->action);
        return $this;
    }

    public function isAnswer(): ToDoWhileHearingMessage
    {
        $this->hearing->isAnswer($this->action);
        return $this;
    }

    public function isChoice($suggestionIndex): ToDoWhileHearingMessage
    {
        $this->hearing->isChoice($suggestionIndex, $this->action);
        return $this;
    }

    public function hasChoice(array $choices): ToDoWhileHearingMessage
    {
        $this->hearing->hasChoice($choices, $this->action);
        return $this;
    }

    public function isEvent(string $eventName): ToDoWhileHearingMessage
    {
        $this->hearing->isEvent($eventName, $this->action);
        return $this;
    }

    public function isEventIn(array $eventName): ToDoWhileHearingMessage
    {
        $this->hearing->isEventIn($eventName, $this->action);
        return $this;
    }

    public function isTypeOf(string $messageType): ToDoWhileHearingMessage
    {
        $this->hearing->isTypeOf($messageType, $this->action);
        return $this;
    }

    public function isInstanceOf(string $messageClazz): ToDoWhileHearingMessage
    {
        $this->hearing->isInstanceOf($messageClazz, $this->action);
        return $this;
    }

    public function feels(string $emotionName): ToDoWhileHearingMessage
    {
        $this->hearing->feels($emotionName, $this->action);
        return $this;
    }

    public function isPositive(): ToDoWhileHearingMessage
    {
        $this->hearing->isPositive($this->action);
        return $this;
    }

    public function isNegative(): ToDoWhileHearingMessage
    {
        $this->hearing->isNegative($this->action);
        return $this;
    }

    public function isAnyIntent(): ToDoWhileHearingMessage
    {
        $this->hearing->isAnyIntent($this->action);
        return $this;
    }

    public function isIntent(string $intentName): ToDoWhileHearingMessage
    {
        $this->hearing->isIntent($intentName, $this->action);
        return $this;
    }

    public function isIntentIn(array $intentNames): ToDoWhileHearingMessage
    {
        $this->hearing->isIntentIn($intentNames, $this->action);
        return $this;
    }

    public function hasEntity(string $entityName): ToDoWhileHearingMessage
    {
        $this->hearing->hasEntity($entityName, $this->action);
        return $this;
    }


}