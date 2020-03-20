<?php


namespace Commune\Components\Predefined\Intents\Navigation;


use Commune\Chatbot\App\Intents\NavigateIntent;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Support\Utils\StringUtils;

class QuitInt extends NavigateIntent
{

    // 定义了意图的命令名称.
    const SIGNATURE = 'quit';

    const DESCRIPTION = '退出当前会话';

    public static function getContextName(): string
    {
        return StringUtils::normalizeContextName('navigation.'.static::SIGNATURE);
    }

    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->quit();
    }

}