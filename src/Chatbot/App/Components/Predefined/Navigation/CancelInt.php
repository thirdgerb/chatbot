<?php


namespace Commune\Chatbot\App\Components\Predefined\Navigation;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class CancelInt extends NavigateIntent
{

    const SIGNATURE = 'cancel';

    const DESCRIPTION = '取消';

    const EXAMPLES = [
        'cancel',
        '退出这个话题',
        '取消吧',
        '结束这个议题',
        '不用继续了',
        '可以取消了',
        '这个事情就这样吧',
        '别继续了',
    ];


    protected static function getContextName(): string
    {
        return 'navigation.'.static::SIGNATURE;
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->cancel();
    }


}