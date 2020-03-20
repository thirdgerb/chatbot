<?php


namespace Commune\Components\Predefined\Intents\Loop;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Support\Utils\StringUtils;

/**
 * 进入循环的下一步
 */
class NextInt extends MessageIntent
{
    const SIGNATURE = 'next';

    const DESCRIPTION = '下一步';

    public static function getContextName(): string
    {
        return StringUtils::normalizeContextName('loop.'.static::SIGNATURE);
    }

}