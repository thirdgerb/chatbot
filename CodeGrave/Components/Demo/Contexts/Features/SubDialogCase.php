<?php

/**
 * Class SubDialogCase
 * @package Commune\Components\Demo\Contexts
 */

namespace Commune\Components\Demo\Contexts\Features;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\Demo\Cases\Maze\MazeInt;

class SubDialogCase extends TaskDef
{
    const DESCRIPTION = 'test sub dialog';

    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
    }

    public function __hearing(Hearing $hearing) : void
    {
        $hearing
            ->is('quit', Redirector::goQuit())
            ->is('fulfill', Redirector::goFulfill())
            ->is('next', Redirector::goNext())
            ->is('maze', Redirector::goStageThrough(['maze']))
            ->is('back', Redirector::goBackward())
            ->is('stage', function(Dialog $dialog){
                $dialog->say()->info("current stage is %stage%", [
                   'stage' => $dialog->currentStage(),
                ]);

                return $dialog->wait();
            });
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->dialog->goStagePipes(['test1', 'test2', 'test3']);
    }

    public function __onTest1(Stage $stage): Navigator
    {
        return $this->runTest($stage);
    }

    public function __onTest2(Stage $stage): Navigator
    {
        return $this->runTest($stage);
    }

    public function __onTest3(Stage $stage): Navigator
    {
        return $this->runTest($stage);
    }

    public function __onMaze(Stage $stage) : Navigator
    {
        return $stage->dependOn(new MazeInt(), function(Dialog $dialog){
            return $dialog->next();
        });
    }

    protected function runTest(Stage $stage) : Navigator
    {
        return $stage->buildTalk([
                'stage' => $stage->name,
            ])
            ->info('enter stage %stage%')
            ->hearing()
            ->end();
    }


}