<?php


namespace Commune\Components\Story\Intents;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Support\Utils\StringUtils;

class ReturnGameInt extends MessageIntent
{
    const DESCRIPTION = '返回游戏';

    public static function getContextName(): string
    {
        return StringUtils::normalizeContextName('story-component.return-game');
    }

}