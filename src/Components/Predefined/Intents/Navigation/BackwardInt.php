<?php


namespace Commune\Components\Predefined\Intents\Navigation;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class BackwardInt extends NavigateIntent
{
    const SIGNATURE = 'backward';

    const DESCRIPTION = '回到上一轮对话';

    public static function getContextName(): string
    {
        return 'navigation.'.static::SIGNATURE;
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->backward();
    }


}