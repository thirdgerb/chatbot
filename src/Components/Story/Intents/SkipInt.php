<?php


namespace Commune\Components\Story\Intents;


use Commune\Chatbot\App\Intents\MessageIntent;

class SkipInt extends MessageIntent
{
    const DESCRIPTION = '跳过内容';

    public static function getContextName(): string
    {
        return 'storyComponent.skip';
    }


}