<?php


namespace Commune\Components\Story\Intents;


use Commune\Chatbot\App\Intents\MessageIntent;

class ChooseEpisodeInt extends MessageIntent
{
    const DESCRIPTION = '选择章节';

    public static function getContextName(): string
    {
        return 'storyComponent.chooseEpisode';
    }

}