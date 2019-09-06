<?php


namespace Commune\Chatbot\App\Components\Predefined\Attitudes;


use Commune\Chatbot\App\Intents\MessageIntent;

abstract class AttitudeInt extends MessageIntent
{
    public static function getContextName(): string
    {
        return 'attitudes.'.static::SIGNATURE;
    }

}