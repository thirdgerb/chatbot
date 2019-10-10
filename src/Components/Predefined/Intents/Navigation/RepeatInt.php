<?php


namespace Commune\Components\Predefined\Intents\Navigation;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class RepeatInt extends NavigateIntent
{


    const SIGNATURE = 'repeat';

    const DESCRIPTION = '重复当前语境';

    public static function getContextName(): string
    {
        return 'navigation.'.static::SIGNATURE;
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->repeat();
    }

}