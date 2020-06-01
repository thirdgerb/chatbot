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

use Commune\Support\Parameter\ParamBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Exiting;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\Operate\Hearing;
use Commune\Components\Demo\Cases\Maze\Logic\Manager;
use Commune\Components\Demo\Cases\Memories\UserPlayedHistory;
use Commune\Host\Contexts\ACodeContext;
use Commune\Host\Contexts\CodeContext\BuildHearing;
use Commune\Host\Contexts\CodeContext\DefineParam;
use Commune\Host\Contexts\CodeContext\OnWithdraw;
use Commune\Blueprint\Ghost\Context\StageBuilder as Stage;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;
use Commune\Protocals\HostMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read UserPlayedHistory $played
 *
 * @property-read int $steps
 * @property-read int $direction
 * @property-read string $cell
 * @property-read int $y
 * @property-read int $x
 * @property-read string[][] $map
 * @property-read string[] $items
 * @property-read null|int $score
 */
class PlayMaze extends ACodeContext implements
    OnWithdraw,
    DefineParam,
    BuildHearing
{
    /**
     * @var UserPlayedHistory
     */
    protected $playedHistory;

    /*------- messages -------*/

    protected $cancelMessage = 'demo.maze.play.cancel';

    protected $locationMessage = 'demo.maze.info.location';

    protected $bornMessage = 'demo.maze.play.born';

    protected $thenWhat = 'demo.maze.dialog.then';

    protected $sameRoomMessage = 'demo.maze.play.sameRoom';

    protected $winMessage = 'demo.maze.play.win';

    protected $endGameMessage = 'demo.maze.play.end';

    protected $noticeMessage = 'demo.maze.play.notice';

    protected $failToEnter = 'demo.maze.play.back';

    protected $cellsMessages = [
        Manager::CELL_BORN => [
            '回到了起点, 这里有一个锁住的电梯, 不知道钥匙在哪里',
            '回到了起点, 这里有一个锁住的电梯',
        ],
        Manager::CELL_NORMAL => [
            '来到了一个纯白色的空房间',
            '来到了一个纯白色的空房间',
        ],
        Manager::CELL_GAS => [
            '这个房间里充满难闻的雾气, 无法呼吸',
            '带着面罩进入了充满雾气的房间',
        ],
        Manager::CELL_DARKNESS => [
            '这个房间里黑咕隆咚, 什么也看不见',
            '火把的光芒, 照亮了黑暗的房间',
        ],
        Manager::CELL_ICE => [
            '房间地面结着厚厚的冰, 根本无法落脚',
            '鞋子踩在房间的冰面上, 发出嘎吱嘎吱的响声',
        ],
        Manager::CELL_WALL => [
            '门外一片沉寂, 脚下是一个深不见底的悬崖.',
        ],
    ];

    protected $foundItems = [
        Manager::ITEM_KEY => '我注意到角落里发着微微的光芒, 走进仔细一看, 是个钥匙! 我把它塞进了口袋',
        Manager::ITEM_MASK => '房顶垂下一根绳子吊着一个防毒面具, 我顺手戴到了脸上',
        Manager::ITEM_SHOES => '我看到房间中间有一双鞋, 穿上还挺合脚的',
        Manager::ITEM_FIRE => '我发现墙壁上插着一个火炬, 伸手拿了起来',
    ];

    protected $directions = [
        Manager::DIRECTION_NORTH => '北方',
        Manager::DIRECTION_WEST => '西方',
        Manager::DIRECTION_SOUTH => '南方',
        Manager::DIRECTION_EAST => '东方',

    ];


    protected $towardMessages = [
        Manager::TOWARD_FRONT => 'demo.maze.toward.front',
        Manager::TOWARD_LEFT => 'demo.maze.toward.left',
        Manager::TOWARD_RIGHT => 'demo.maze.toward.right',
        Manager::TOWARD_BACK => 'demo.maze.toward.back',
    ];

    /*------------ logic ---------*/

    public static function __params(ParamBuilder $param): ParamBuilder
    {
        return $param
            ->def('steps', 0 )
            ->def('map', [], 'array')
            ->def('direction', 0)
            ->def('cell', Manager::CELL_BORN)
            ->def('y', Manager::BORN_LOCATION[0])
            ->def('x', Manager::BORN_LOCATION[1])
            ->def('items[]', [], 'string')
            ->def('toward', null, 'string');
    }


    public function __withdraw(Exiting $dialog): ? Dialog
    {
        if ($dialog->isEvent(Dialog::CANCEL)) {
            $dialog->send()->info($this->cancelMessage);
        }

        return null;
    }


    public function __hearing(Hearing $hearing): Hearing
    {
        return $hearing
            ->todo($hearing->nav()->next('endGame'))
            ->is('作弊')

            // 听到 '退出' 时 退出.
            ->todo($hearing->nav()->cancel())
            ->is('退出')
            ->is('quit')

            // 听到 '坐标' 时 查看坐标.
            ->todo(function(Dialog $dialog) {

                return $dialog
                    ->send()
                    ->info(
                        $this->locationMessage,
                        [
                            'x' => $this->x + 1,
                            'y' => 5 - $this->y,
                            'direction' => $this->directions[$this->direction]
                        ]
                    )
                    ->over()
                    ->redirect()
                    ->reactivate();
            })
                ->needHelp()
                ->is('坐标')
                ->isIntent(LocationInt::class)

            ->then()
            // 注册默认 end 方法.
            ->defaultFallback(function(Dialog $dialog){
                return $dialog
                    ->send()
                    ->info($this->noticeMessage)
                    ->over()
                    ->await();
            });
    }


    protected function initialize() : void
    {
        $props = [
            'map' => Manager::initializeMap(),
            'steps' => 0,
            'direction' => Manager::DIRECTION_NORTH,
            'cell' => Manager::CELL_BORN,
            'y' => Manager::BORN_LOCATION[0],
            'x' => Manager::BORN_LOCATION[1],
            'items' => [],
            'toward' => null,
        ];

        // 游戏次数增加.
        $this->played->total = $this->played->total + 1;
        $this->merge($props);
    }


    protected function parseTowardAndGo(Dialog $dialog, string $input) : ? Dialog
    {
        if (strstr($input, '前')) {
            return $this->goToward($dialog, Manager::TOWARD_FRONT);
        }

        if (strstr($input, '后')) {
            return $this->goToward($dialog, Manager::TOWARD_BACK);
        }

        if (strstr($input, '左')) {
            return $this->goToward($dialog, Manager::TOWARD_LEFT);
        }

        if (strstr($input, '右')) {
            return $this->goToward($dialog, Manager::TOWARD_RIGHT);
        }

        return null;
    }


    public function goToward(Dialog $dialog, int $toward) : Dialog
    {
        $this->steps = $this->steps + 1;

        $first = $this->towardMessages[$toward];

        $direction = Manager::parseTowardToDirection($this->direction, $toward);
        list ($y, $x) = Manager::parseLocationOfDirection($this->y, $this->x, $direction);

        list($cell, $success, $item, $win) = Manager::goLocation($this->map, $y, $x, $this->items);

        // 进入房间的消息.
        // 进入房间失败.
        if (!$success) {
            $middle = $this->cellsMessages[$cell][0];

            // 是同一个房间.
        } elseif ($cell === $this->cell) {
            $middle = $this->sameRoomMessage;

            // 进入房间成功.
        } else {
            $middle = $this->cellsMessages[$cell][1];
        }

        // 找到道具的消息.
        if (isset($item)) {
            $itemMessage = $this->foundItems[$item];
            $items = $this->items;
            $items[] = $item;
            $this->items = $items;

        } else {
            $itemMessage = '';
        }

        // 胜利消息.
        if ($win) {
            $last = $this->winMessage;

        } elseif ($success) {
            $last = '';

            // 没有进房间, 退出来.
        } else {

            $last = $this->failToEnter;
        }
        $speech = $dialog->send();
        $speech->info($first);
        $paragraph = '';
        foreach ([$middle, $itemMessage, $last] as $info) {
            if (!empty($info)) {
                $paragraph .= "$info\n";
            }
        }
        $speech->info($paragraph);

        // 赢了, 游戏结束
        if ($win) {
            return $dialog->redirect()->next('end_game');

            // 走进了另一张门
        } elseif ($success) {
            $this->y = $y;
            $this->x = $x;
            $this->direction = $direction;
            $this->cell = $cell;

            return $dialog->redirect()->reactivate();

            // 退了回去
        } else {
            // 面向不改.
            $this->cell = null; // 下一次一定介绍.
            return $dialog->redirect()->reactivate();
        }
    }

    protected function hearToward(int $toward) : \Closure
    {
        return function(Dialog $dialog) use ($toward) {
            return $this->goToward($dialog, $toward);
        };
    }

    /*------------ stages ---------*/


    /**
     * 游戏开始.
     *
     * @param Stage $stage
     * @return StageDef
     */
    public function __on_start(Stage $stage): StageDef
    {
        return $stage->onActivate(function(Dialog $dialog){
            return $dialog
                ->send()
                ->info($this->bornMessage)
                ->over()
                ->redirect()
                ->next('play');
        })->end();
    }


    /**
     * 运行游戏的回合.
     *
     * @param Stage $stage
     * @return StageDef
     */
    public function __on_play(Stage $stage) : StageDef
    {
        return $stage
            ->onActivate(function(Dialog $dialog){

                return $dialog
                    ->await(
                        [
                            MazeFrontInt::ucl(),
                            MazeBackInt::ucl(),
                            MazeLeftInt::ucl(),
                            MazeRightInt::ucl(),
                        ]
                    )
                    ->askChoose(
                        $this->thenWhat,
                        [
                            '前',
                            '后',
                            '左',
                            '右',
                        ]
                    );

            })
            ->onHeed(function(Dialog $dialog){

                return $dialog
                    ->hearing()
                    ->isIntent(TowardsInt::class)
                        ->then(function(Dialog $dialog, TowardsInt $towards) : ? Dialog {
                            return $this->parseTowardAndGo($dialog, $towards->toward);

                        })
                    ->todo($this->hearToward(Manager::TOWARD_FRONT))
                        ->isIntent(MazeFrontInt::class)
                        ->isChoice(0)
                    ->todo($this->hearToward(Manager::TOWARD_BACK))
                        ->isIntent(MazeBackInt::class)
                        ->isChoice(1)
                    ->todo($this->hearToward(Manager::TOWARD_LEFT))
                        ->isIntent(MazeLeftInt::class)
                        ->isChoice(2)
                    ->todo($this->hearToward(Manager::TOWARD_RIGHT))
                        ->isIntent(MazeRightInt::class)
                        ->isChoice(3)
                    ->todo(function(HostMsg $message, Dialog $dialog){
                        return $this->parseTowardAndGo(
                            $dialog,
                            $message->getText()
                        );
                    })
                        ->isInstanceOf(VerbalMsg::class)
                    ->end();

            })
            ->end();
    }



    public function __on_end_game(Stage $stage) : StageDef
    {
        return $stage->onActivate(function(Dialog $dialog){

            $this->score = Manager::makePoints($this->steps);

            return $dialog->send()
                ->info($this->endGameMessage, ['score' => $this->score])
                ->over()
                ->redirect()
                ->fulfill();
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