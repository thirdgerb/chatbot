<?php


namespace Commune\Chatbot\App\Components\Predefined\Navigation;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class RestartInt extends NavigateIntent
{

    const SIGNATURE = 'restart';

    const DESCRIPTION = '重启当前语境';

    public static function getContextName(): string
    {
        return 'navigation.'.static::SIGNATURE;
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->restart();
    }


}