<?php


namespace Commune\Components\Demo\Cases\Maze;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Intents\TaskIntent;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

use Commune\Components\Demo\Cases\Maze\Memories\UserPlayHistory;
use Commune\Components\Demo\Cases\Maze\Tasks\PlayMaze;

/**
 * @property-read UserPlayHistory $played

 */
class MazeInt extends TaskIntent
{

    const SIGNATURE = 'maze';
    const DESCRIPTION = '迷宫小游戏';

    /*------------ 临时属性 -----------*/

    /**
     * @var UserPlayHistory
     */
    protected $playedHistory;

    /*------------ messages -----------*/

    protected $quitGame = 'demo.maze.info.quit';

    protected $welcome = 'demo.maze.info.welcome';

    protected $introduce = 'demo.maze.info.introduce';

    protected $welcomeOldPlayer = 'demo.maze.info.welcomeOldPlayer';

    protected $oneMore = 'demo.maze.dialog.oneMore';

    protected $farewellMessage = 'demo.maze.info.farewell';

    protected $wantIntro = 'demo.maze.dialog.wantIntro';

    protected $confirmStart = 'demo.maze.dialog.start';


    /*------------ messages -----------*/

    public function __construct()
    {
        parent::__construct([]);
    }

    /*----------------- 必须实现方法 ----------------*/

    public function __exiting(Exiting $listener): void
    {
        $listener->onCancel(function(Dialog $dialog) {
            $dialog->say()->info($this->quitGame);
        });
    }


    public static function __depend(Depending $depending): void
    {
    }

    /**
     * 全局 hearing
     * @param Hearing $hearing
     */
    public function __hearing(Hearing $hearing)
    {
        $hearing

            // 听到 '退出' 时 退出.
            ->todo(Redirector::goCancel())
                ->is('退出')
                ->is('quit')

            ->otherwise()
            ->defaultFallback(function(Dialog $dialog){
                $dialog->say()->info('没有明白您的意思, 可以说"退出"以退出游戏.');
                return $dialog->rewind();
            });
    }

    /*------------ stages ------------*/

    /**
     * 游戏开始.
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onStart(Stage $stage): Navigator
    {

        // 用户不是第一次玩.
        if ($this->played->total > 0) {
            return $stage->dialog->goStage('oldPlayer');
        }

        // 用户第一次玩.
        return $stage->buildTalk()
            ->info($this->welcome)
            ->askConfirm($this->wantIntro, true)
            ->hearing()
            // negative
            ->todo(Redirector::goStage('born'))
                ->hasKeywords([['不', '别']])
                ->isNegative()
            // positive
            ->todo(Redirector::goStage('intro'))
                ->hasKeywords([['是', '好', '要', '可以', '开始']])
                ->isPositive()
            // 默认就进入, 避免用户
            ->end(Redirector::goStage('intro'));
    }

    /**
     * 欢迎老玩家.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onOldPlayer(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->info($this->welcomeOldPlayer, [
                'total' => $this->played->total,
                'score' => $this->played->highestScore,
            ])
            ->askConfirm($this->confirmStart)
            ->hearing()
            ->isPositive(Redirector::goStage('born'))
            ->isNegative(Redirector::goFulfill())
            // 避免输入错误. 默认
            ->end(Redirector::goStage('born'));
    }


    /**
     * 介绍游戏.
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onIntro(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->info($this->introduce)
            ->askConfirm($this->confirmStart)
            ->hearing()
            ->isPositive(Redirector::goStage('born'))
            ->isNegative(Redirector::goFulfill())
            ->end();
    }


    /**
     * 游戏初始化.
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onBorn(Stage $stage) : Navigator
    {
        return $stage->dependOn(new PlayMaze(), function(PlayMaze $playMaze, Dialog $dialog){
            $score = $playMaze->score;
            if ($this->played->highestScore < $score ) {
                $this->played->highestScore = $score;
            }
            return $dialog->goStage('oneMore');
        });

    }

    /**
     * 询问是否再来一局.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onOneMore(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askConfirm($this->oneMore)
            ->hearing()
                ->isPositive(Redirector::goStage('born'))
                ->isNegative(function(Dialog $dialog) {
                    $dialog->say()->info($this->farewellMessage);
                    return $dialog->fulfill();
                })
                ->end(Redirector::goFulfill());
    }


    /*------------ mutator ---------*/

    public function __getPlayed() : UserPlayHistory
    {
        return $this->playedHistory
            ?? $this->playedHistory = UserPlayHistory::from($this);
    }

}