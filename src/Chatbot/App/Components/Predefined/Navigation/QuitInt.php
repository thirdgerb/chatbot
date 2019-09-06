<?php


namespace Commune\Chatbot\App\Components\Predefined\Navigation;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class QuitInt extends NavigateIntent
{

    const SIGNATURE = 'quit';

    const DESCRIPTION = '退出当前会话';

    public static function getContextName(): string
    {
        return 'navigation.'.static::SIGNATURE;
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->quit();
    }


}