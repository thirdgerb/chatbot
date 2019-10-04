<?php


namespace Commune\Chatbot\Framework\Messages;


use Commune\Chatbot\Blueprint\Message\UnsupportedMsg;

class Unsupported extends AbsMessage implements UnsupportedMsg
{
    public function isEmpty(): bool
    {
        return true;
    }

    public function getText(): string
    {
        return '';
    }

    public function toMessageData(): array
    {
        return [];
    }

}