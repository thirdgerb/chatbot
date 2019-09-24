<?php


namespace Commune\Chatbot\App\Components\Predefined\Dialogue;

use Commune\Chatbot\App\Intents\MessageIntent;

/**
 * 帮助
 */
class HelpInt extends MessageIntent
{
    const SIGNATURE = 'help';
    const DESCRIPTION = '帮助';

    public static function getContextName(): string
    {
        return 'dialogue.help';
    }

}