<?php


namespace Commune\Components\Predefined\Intents\Attitudes;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Support\Utils\StringUtils;

/**
 * 描述用户态度的默认intent. 要用得上.
 */
abstract class AttitudeInt extends MessageIntent
{
    public static function getContextName(): string
    {
        return StringUtils::normalizeContextName('attitudes.'.static::SIGNATURE);
    }

}
