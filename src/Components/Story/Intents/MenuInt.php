<?php


namespace Commune\Components\Story\Intents;


use Commune\Chatbot\App\Intents\MessageIntent;

class MenuInt extends MessageIntent
{
    const DESCRIPTION = '打开菜单';

    public static function getContextName(): string
    {
        return 'storyComponent.menu';
    }

}