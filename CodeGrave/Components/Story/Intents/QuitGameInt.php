<?php


namespace Commune\Components\Story\Intents;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Support\Utils\StringUtils;

class QuitGameInt extends MessageIntent
{
    const DESCRIPTION = '退出游戏';

    public static function getContextName(): string
    {
        return StringUtils::normalizeContextName('story-component.quit-game');
    }

}