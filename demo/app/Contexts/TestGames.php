<?php


namespace Commune\Demo\App\Contexts;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Callables\StageComponents\Menu;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Demo\App\Cases\Maze\MazeInt;
use Commune\Studio\Components\Demo\Guest\GuessNum;

class TestGames extends TaskDef
{
    const DESCRIPTION = 'demo.contexts.testGames';

    public static function __depend(Depending $depending): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            ->info('demo.dialog.introduceGames')
            ->toStage()
            ->component(
            (new Menu(
                'demo.dialog.chooseGames',
                [
                    MazeInt::class,
                    'story.examples.sanguo.changbanpo',
                    GuessNum::class,
                ]
            ))->onFallback(Redirector::goFulfill())
        );
    }

    public function __exiting(Exiting $listener): void
    {
    }


}