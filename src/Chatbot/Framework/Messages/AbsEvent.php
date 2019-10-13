<?php


namespace Commune\Chatbot\Framework\Messages;


use Commune\Chatbot\Blueprint\Message\Event\EventMsg;

class AbsEvent extends AbsConvoMsg implements EventMsg
{

    public function getEventName(): string
    {
        return static::class;
    }

    public function isEmpty(): bool
    {
        return false;
    }

    public function getText(): string
    {
        return $this->getEventName();
    }

    public function toMessageData(): array
    {
        return [
            'event' => $this->getEventName()
        ] + parent::toMessageData();
    }


    public static function mock()
    {
        return new static();
    }
}