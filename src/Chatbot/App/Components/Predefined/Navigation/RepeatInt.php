<?php


namespace Commune\Chatbot\App\Components\Predefined\Navigation;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class RepeatInt extends NavigateIntent
{


    const SIGNATURE = 'repeat';

    const DESCRIPTION = '重复当前语境.';

    const EXAMPLES = [
        'repeat',
        '再说一遍',
        '刚才说什么',
        '现在说的是啥',
        '您问什么',
        '您要干什么来着',
        '您刚才要我干嘛',
    ];


    protected static function getContextName(): string
    {
        return 'navigation.'.static::SIGNATURE;
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->repeat();
    }

}