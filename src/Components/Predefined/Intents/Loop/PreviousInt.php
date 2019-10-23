<?php

/**
 * Class PreviousInt
 * @package Commune\Components\Predefined\Intents\Loop
 */

namespace Commune\Components\Predefined\Intents\Loop;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Support\Utils\StringUtils;

/**
 * 返回循环的上一步
 */
class PreviousInt extends MessageIntent
{
    const SIGNATURE = 'previous';

    const DESCRIPTION = '上一步';

    public static function getContextName(): string
    {
        return StringUtils::normalizeContextName('loop.'.static::SIGNATURE);
    }

}