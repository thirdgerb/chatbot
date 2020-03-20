<?php

/**
 * Class HomeInt
 * @package Commune\Components\Predefined\Intents\Navigation
 */

namespace Commune\Components\Predefined\Intents\Navigation;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Support\Utils\StringUtils;

class HomeInt extends NavigateIntent
{

    const SIGNATURE = 'home';

    const DESCRIPTION = '回到起点';


    public static function getContextName(): string
    {
        return StringUtils::normalizeContextName('navigation.'.static::SIGNATURE);
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->redirect->home();
    }


}