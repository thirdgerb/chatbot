<?php

/**
 * Class PreviousInt
 * @package Commune\Chatbot\App\Components\Predefined\Loop
 */

namespace Commune\Chatbot\App\Components\Predefined\Loop;


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