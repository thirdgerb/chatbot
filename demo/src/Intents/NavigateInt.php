<?php

namespace Commune\Demo\Intents;

use Commune\Chatbot\OOHost\Context\Intent\AbsCmdIntent;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\Demo\Cases\Maze\MazeInt;

/**
 * 模拟导航类意图
 */
class NavigateInt extends AbsCmdIntent
{
    const SIGNATURE = 'testNav';

    const DESCRIPTION = '模拟导航类意图';

    // 模拟用关键字来命中意图
    const KEYWORDS = [
        '迷宫',
    ];

    public static function getContextName(): string
    {
        return 'demo.lesions.nav';
    }

    // 直接进入迷宫程序.
    public function navigate(Dialog $dialog): ? Navigator
    {
        return $dialog->redirect->sleepTo(MazeInt::class);
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->dialog->fulfill();
    }
}