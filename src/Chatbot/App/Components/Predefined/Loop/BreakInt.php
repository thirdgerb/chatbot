<?php


namespace Commune\Chatbot\App\Components\Predefined\Loop;


use Commune\Chatbot\App\Intents\MessageIntent;

/**
 * 打破循环.
 */
class BreakInt extends MessageIntent
{
    const SIGNATURE = 'break';

    const DESCRIPTION = '退出循环';

    public static function getContextName(): string
    {
        return 'loop.'.static::SIGNATURE;
    }


}