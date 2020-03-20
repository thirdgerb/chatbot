<?php


namespace Commune\Components\Predefined\Intents\Loop;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Support\Utils\StringUtils;

/**
 * 打破循环. 取消.
 */
class BreakInt extends MessageIntent
{
    const SIGNATURE = 'break';

    const DESCRIPTION = '退出循环';

    public static function getContextName(): string
    {
        return StringUtils::normalizeContextName('loop.'.static::SIGNATURE);
    }


}