<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Demo\Cases;

use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\Tools\Hearing;
use Commune\Components\Demo\Cases\Memories\UserPlayedHistory;
use Commune\Host\Contexts\ACodeContext;
use Commune\Host\Contexts\CodeContext\BuildHearing;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read UserPlayedHistory $played
 */
class Maze extends ACodeContext implements
    BuildHearing
{
    const DESCRIPTION = '方向迷宫';

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


    public function __hearing(Hearing $hearing): Hearing
    {
        $hearing
            // 听到 '退出' 时 退出.
            ->todo($hearing->nav()->cancel())
            ->is('退出')
            ->is('quit')

            ->then()
            ->defaultFallback(function(Dialog $dialog){
                $dialog->send()->info('没有明白您的意思, 可以说"退出"以退出游戏.');
                return $dialog->nav()->rewind();
            });
    }

    public function __on_start(StageBuilder $stage): StageDef
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {
                if ($this->played->total > 0) {
                    return $dialog->nav()->next('old_player');
                }

                return $dialog->send()
                    ->info($this->welcome)
                    ->over()
                    ->await()
                    ->askConfirm(
                        $this->welcome,
                        true
                    );
            })
            ->onEvent(
                Dialog::HEED,
                function (Dialog $dialog) {

                    return $dialog
                        ->hearing()
                        ->todo($dialog->nav()->next('born'))
                            ->hasKeywords([['不', '别']])
                            ->isNegative()
                        ->todo($dialog->nav()->next('intro'))
                            ->hasKeywords([['是', '好', '要', '可以', '开始']])
                            ->isPositive()
                        ->end($dialog->nav()->next('intro'));
                }
            )
            ->end();
    }

    public function __on_old_player(StageBuilder $stage) : StageDef
    {
        return $stage
            ->onActivate(function(Dialog $dialog){

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
            ->onRetain(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->isPositive()
                        ->then($goBorn = $dialog->nav()->next('born'))
                    ->isNegative()
                        ->then($dialog->nav()->fulfill())
                    ->end($goBorn);
            })
            ->end();
    }


    /**
     * 介绍游戏.
     *
     * @param StageBuilder $stage
     * @return StageDef
     */
    public function __on_intro(StageBuilder $stage) : StageDef
    {
        return $stage
            ->onActivate(function(Dialog $dialog)  {

                return $dialog
                    ->send()
                    ->info($this->introduce)
                    ->over()
                    ->await()
                    ->askConfirm($this->confirmStart);

            })
            ->onEvent(
                Dialog::HEED,
                function(Dialog $dialog) {
                    return $dialog
                        ->hearing()
                        ->isPositive()
                            ->then($dialog->nav()->next('born'))
                        ->isNegative()
                            ->then($dialog->nav()->fulfill())
                        ->end();
                }
            )
            ->end();
    }

    /**
     * 游戏初始化.
     *
     * @param StageBuilder $stage
     * @return StageDef
     */
    public function __on_born(StageBuilder $stage) : StageDef
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {
                return $dialog
                    ->nav()
                    ->dependOn(PlayMaze::ucl());
            })
            ->onRetain(function (Dialog $dialog) {

                /**
                 * @var PlayMaze $context
                 */
                $context = $dialog->getContext(PlayMaze::class);
                $score = $context->score;

                if ($this->played->highestScore < $score ) {
                    $this->played->highestScore = $score;
                }

                return $dialog->nav()->next('one_more');
            })
            ->end();
    }

    /**
     * 询问是否再来一局.
     * @param StageBuilder $stage
     * @return StageDef
     */
    public function __on_one_more(StageBuilder $stage) : StageDef
    {
        return $stage
            ->onActivate(function(Dialog $dialog){

                return $dialog
                    ->await()
                    ->askConfirm($this->oneMore);
            })
            ->onRetain(function(Dialog $dialog) : Dialog {

                return $dialog
                    ->hearing()
                    ->isPositive()
                    ->then($dialog->nav()->next('born'))
                    ->isNegative()
                    ->then(function(Dialog $dialog){
                        return $dialog
                            ->send()
                            ->info($this->farewellMessage)
                            ->over()
                            ->nav()
                            ->fulfill();
                    })
                    ->end($dialog->nav()->fulfill());

            })
            ->end();

    }



    /*------------ mutator ---------*/

    public function __get_played() : UserPlayedHistory
    {
        return $this->playedHistory
            ?? $this->playedHistory = UserPlayedHistory::find($this->getCloner());
    }
}