<?php

/**
 * Class HomeInt
 * @package Commune\Chatbot\App\Components\Predefined\Navigation
 */

namespace Commune\Chatbot\App\Components\Predefined\Navigation;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class HomeInt extends NavigateIntent
{

    const SIGNATURE = 'home';

    const DESCRIPTION = '回到起点';


    public static function getContextName(): string
    {
        return 'navigation.'.static::SIGNATURE;
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->redirect->home();
    }


}