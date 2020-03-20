<?php


namespace Commune\Components\Predefined\Intents\Dialogue;


use Commune\Chatbot\App\Intents\MessageIntent;

class RandomInt  extends MessageIntent
{
    const SIGNATURE = 'random';
    const DESCRIPTION = '随便';

    public static function getContextName(): string
    {
        return 'dialogue.random';
    }

}