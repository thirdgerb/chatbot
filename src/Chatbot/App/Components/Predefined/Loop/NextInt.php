<?php


namespace Commune\Chatbot\App\Components\Predefined\Loop;


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