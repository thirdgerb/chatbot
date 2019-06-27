<?php

namespace Commune\Demo\App\Intents;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class QuitInt extends NavigateIntent
{
    const NAME = 'navigation.quit';

    const SIGNATURE = 'quit';
    const DESCRIPTION = '退出';
    const REGEX = [
        ['/^(quit|bye|再见)$/iu']
    ];

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->quit();
    }

    public static function getContextName(): string
    {
        return static::NAME;
    }

    public function __exiting(Exiting $listener): void
    {
    }


}