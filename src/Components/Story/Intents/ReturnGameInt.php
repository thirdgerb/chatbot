<?php


namespace Commune\Components\Story\Intents;


use Commune\Chatbot\App\Intents\MessageIntent;

class ReturnGameInt extends MessageIntent
{
    const DESCRIPTION = '返回游戏';

    public static function getContextName(): string
    {
        return 'storyComponent.returnGame';
    }

}