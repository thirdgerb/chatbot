<?php


namespace Commune\Components\Predefined\Intents\Loop;


use Commune\Chatbot\App\Intents\MessageIntent;

class NextInt extends MessageIntent
{
    const SIGNATURE = 'next';

    const DESCRIPTION = '下一个';

    public static function getContextName(): string
    {
        return 'loop.'.static::SIGNATURE;
    }

}