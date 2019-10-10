<?php


namespace Commune\Components\Story\Intents;


use Commune\Chatbot\App\Intents\MessageIntent;

class QuitGameInt extends MessageIntent
{
    const DESCRIPTION = '退出游戏';

    public static function getContextName(): string
    {
        return 'storyComponent.quitGame';
    }

}