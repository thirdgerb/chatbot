<?php


namespace Commune\Components\Predefined\Intents\Loop;


use Commune\Chatbot\App\Intents\MessageIntent;

/**
 * 进入循环的下一步
 */
class NextInt extends MessageIntent
{
    const SIGNATURE = 'next';

    const DESCRIPTION = '下一步';

    public static function getContextName(): string
    {
        return 'loop.'.static::SIGNATURE;
    }

}