<?php

namespace Commune\Components\Predefined\Intents\Navigation;

use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Support\Utils\StringUtils;

class CancelInt extends NavigateIntent
{

    const SIGNATURE = 'cancel';

    const DESCRIPTION = '退出当前语境';

    public static function getContextName(): string
    {
        return StringUtils::normalizeContextName('navigation.'.static::SIGNATURE);
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->cancel();
    }


}