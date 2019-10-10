<?php

/**
 * Class PreviousInt
 * @package Commune\Components\Predefined\Intents\Loop
 */

namespace Commune\Components\Predefined\Intents\Loop;


use Commune\Chatbot\App\Intents\MessageIntent;

class PreviousInt extends MessageIntent
{
    const SIGNATURE = 'previous';

    const DESCRIPTION = '上一个';

    public static function getContextName(): string
    {
        return 'loop.'.static::SIGNATURE;
    }

}