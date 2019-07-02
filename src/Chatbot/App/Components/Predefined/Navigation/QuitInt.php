<?php


namespace Commune\Chatbot\App\Components\Predefined\Navigation;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class QuitInt extends NavigateIntent
{

    const SIGNATURE = 'quit';

    const DESCRIPTION = '退出当前会话';

    const EXAMPLES = [
        'exit',
        '结束对话',
        '今天先说到这儿',
        '就说到这里吧',
        '对话可以结束了',
        '今天的对话到此为止了',
    ];


    protected static function getContextName(): string
    {
        return 'navigation.'.static::SIGNATURE;
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->quit();
    }


}