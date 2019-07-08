<?php


namespace Commune\Chatbot\App\Components\Predefined\Navigation;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

class RestartInt extends NavigateIntent
{

    const SIGNATURE = 'restart';

    const DESCRIPTION = '重启当前语境';

    const EXAMPLES = [
        'restart',
        '重来一遍吧',
        '从头开始吧',
        '重新来一次',
        '从第一步再来',
        '能不能重新开始',
        '我想要重来一遍',
    ];

    protected static function getContextName(): string
    {
        return 'navigation.'.static::SIGNATURE;
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->restart();
    }


}