<?php


namespace Commune\Components\Predefined\Intents\Loop;


use Commune\Chatbot\App\Intents\MessageIntent;

/**
 * 返回循环的头部
 */
class RewindInt extends MessageIntent
{
    const SIGNATURE = 'rewind';

    const DESCRIPTION = '从头开始';

    public static function getContextName(): string
    {
        return 'loop.'.static::SIGNATURE;
    }


}