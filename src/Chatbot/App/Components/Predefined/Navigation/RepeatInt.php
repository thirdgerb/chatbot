<?php


namespace Commune\Chatbot\App\Components\Predefined\Navigation;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class RepeatInt extends NavigateIntent
{


    const SIGNATURE = 'repeat';

    const DESCRIPTION = '重复机器人的话';

    const EXAMPLES = [
        'repeat',
        '再说一遍',
        '刚才说什么',
        '现在说的是啥',
        '你问什么',
        '你要干什么来着',
        '你刚才要我干嘛',
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