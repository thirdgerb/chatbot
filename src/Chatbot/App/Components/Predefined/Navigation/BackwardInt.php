<?php


namespace Commune\Chatbot\App\Components\Predefined\Navigation;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class BackwardInt extends NavigateIntent
{
    const SIGNATURE = 'back';

    const DESCRIPTION = '回到上一轮对话';

    const EXAMPLES = [
        '回到上一步',
        '返回上一步',
        '回到上个问题',
        '回到刚才那个问题',
        '返回前面的问题',
        '再说一次上一个问题',
        'last step',
    ];


    protected static function getContextName(): string
    {
        return 'navigation.'.static::SIGNATURE;
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->backward();
    }


}