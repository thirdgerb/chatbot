<?php


namespace Commune\Components\Story\Intents;


use Commune\Chatbot\App\Intents\MessageIntent;
use Commune\Support\Utils\StringUtils;

class ChooseEpisodeInt extends MessageIntent
{
    const DESCRIPTION = '选择章节';

    public static function getContextName(): string
    {
        return StringUtils::normalizeContextName('story-component.choose-episode');
    }

}