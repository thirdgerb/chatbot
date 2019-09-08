<?php


namespace Commune\Chatbot\App\Components\Predefined\Attitudes;


use Commune\Chatbot\App\Intents\MessageIntent;

/**
 * 描述用户态度的默认intent. 要用得上.
 */
abstract class AttitudeInt extends MessageIntent
{
    public static function getContextName(): string
    {
        return 'attitudes.'.static::SIGNATURE;
    }

}
