<?php


namespace Commune\Demo\App\Cases\Maze;


use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * @property-read int $times
 * @property-read int $score
 */
class MazeTask extends TaskDef
{
    const DESCRIPTION = '迷宫小游戏';

    public function __onStart(Stage $stage): Navigator
    {
    }

    public function __exiting(Exiting $listener): void
    {
        $quit = function(Dialog $dialog) {
            $dialog->say()->info('退出后, 进度会保存');
        };

        $listener->onCancel($quit);
    }


    public static function __depend(Depending $depending): void
    {
    }


}