<?php


namespace Commune\Chatbot\App\Components\Predefined\Loop;


use Commune\Chatbot\App\Intents\MessageIntent;

class ContinueInt extends MessageIntent
{
    const SIGNATURE = 'continue';

    const DESCRIPTION = '继续';

    public static function getContextName(): string
    {
        return 'loop.'.static::SIGNATURE;
    }


}