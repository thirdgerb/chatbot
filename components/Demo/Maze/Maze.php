<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Demo\Maze;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Context\ACodeContext;
use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder as Stage;
use Commune\Components\Demo\Maze\Memories\UserPlayHistory;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read UserPlayHistory $played
 * @property Ucl|null $play
 *
 *
 * @title 迷宫小游戏
 * @desc 迷宫小游戏
 */
class Maze extends ACodeContext
{
    const CONTEXT_PREFIX = 'commune.demo.maze.';

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

    /*----------------- 实现方法 ----------------*/

    public static function __name(): string
    {
        return self::CONTEXT_PREFIX . 'welcome';
    }

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            'onCancel' => 'cancel_game',
        ]);
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }


    /*----------------- quit ----------------*/

    public function __on_cancel_game(Stage $stage) : Stage
    {
        return $stage->always(function(Dialog $dialog) {
            return $dialog->send()
                ->info($this->quitGame)
                ->over()
                ->cancel();
        });

    }

    /*----------------- 路径 ----------------*/

    public function __on_start(Stage $stage): Stage
    {

        // 用户不是第一次玩.
        if ($this->played->total > 0) {
            return $stage->always($stage->dialog->goStage('old_player'));
        }

        // 用户第一次玩.
        return $stage
            ->onActivate(function(Dialog $dialog) {

                return $dialog
                    ->send()
                    ->info($this->welcome)
                    ->over()
                    ->await()
                    ->askConfirm($this->wantIntro, true);

            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->todo($dialog->goStage('born'))
                        ->hasKeywords([['不', '别']])
                        ->isNegative()
                    ->todo($dialog->goStage('intro'))
                        ->hasKeywords([['是', '好', '要', '可以', '开始']])
                        ->isPositive()
                    ->end($dialog->goStage('intro'));
            });

    }

    /**
     * @title 介绍游戏.
     *
     * @param Stage $stage
     * @return Stage
     */
    public function __on_intro(Stage $stage) : Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {
                return $dialog
                    ->send()
                    ->info($this->introduce)
                    ->over()
                    ->await()
                    ->askConfirm($this->confirmStart);

            })
            ->onReceive(function(Dialog $dialog) {
                return $dialog
                    ->hearing()
                    ->isPositive()
                        ->then($dialog->goStage('born'))
                    ->isNegative()
                        ->then($dialog->fulfill())
                    ->end();
            });
    }


    /**
     * @title 欢迎老玩家.
     * @param Stage $stage
     * @return Stage
     */
    public function __onOldPlayer(Stage $stage) : Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {

                return $dialog
                    ->send()
                    ->info($this->welcomeOldPlayer, [
                        'total' => $this->played->total,
                        'score' => $this->played->highestScore,
                    ])
                    ->over()
                    ->await()
                    ->askConfirm($this->confirmStart);

            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->isPositive()
                        ->then($dialog->goStage('born'))
                    ->isNegative()
                        ->then($dialog->fulfill())
                    // 避免输入错误. 默认
                    ->end($dialog->goStage('born'));
            });
    }


    /**
     * @title 游戏初始化.
     *
     * @param Stage $stage
     * @return Stage
     */
    public function __on_born(Stage $stage) : Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {

                return $dialog->dependOn(
                    PlayMaze::genUcl(),
                    'play'
                );
            })
            ->onResume(function(Dialog $dialog) {
                /**
                 * @var PlayMaze $playMaze
                 */
                $playMaze = $this->play->findContext($dialog->cloner);
                $score = $playMaze->score;
                if ($this->played->highestScore < $score ) {
                    $this->played->highestScore = $score;
                }

                return $dialog->goStage('one_more');
            });
    }




    /**
     * 询问是否再来一局.
     * @param Stage $stage
     * @return Stage
     */
    public function __on_one_more(Stage $stage) : Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {

                return $dialog
                    ->await()
                    ->askConfirm($this->oneMore);
            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->isPositive()
                        ->then($dialog->goStage('born'))
                    ->isNegative()
                        ->then(function(Dialog $dialog) {
                            return $dialog
                                ->send()
                                ->info($this->farewellMessage)
                                ->over()
                                ->fulfill();
                        })
                    ->end($dialog->fulfill());
            });
    }

    /*------------ mutator ---------*/

    public function __get_played() : UserPlayHistory
    {
        return $this->playedHistory
            ?? $this->playedHistory = UserPlayHistory::from($this);
    }

}