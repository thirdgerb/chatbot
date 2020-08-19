<?php

/**
 * Class PlayMaze
 * @package Commune\Components\Demo\Maze
 */

namespace Commune\Components\Demo\Maze;

use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder as Stage;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Tools\Matcher;
use Commune\Components\Demo\Maze\Logic\Manager;
use Commune\Components\Demo\Maze\Memories\UserPlayHistory;
use Commune\Ghost\Context\ACodeContext;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;


/**
 * 进行迷宫
 *
 * @property-read UserPlayHistory $played
 *
 * @property-read int $steps
 * @property-read int $direction
 * @property-read string $cell
 * @property-read int $y
 * @property-read int $x
 * @property-read string[][] $map
 * @property-read string[] $items
 * @property-read null|int $score
 *
 *
 * @title 运行迷宫游戏
 * @desc 运行迷宫游戏
 */
class PlayMaze extends ACodeContext
{
    const DESCRIPTION = '运行迷宫游戏';

    /**
     * @var UserPlayHistory
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

    /*-------------- 参数定义 ---------------*/

    public static function __name(): string
    {
        return Maze::CONTEXT_PREFIX . 'play';
    }

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([

            'memoryAttrs' => [
                'map' => [],
                'steps' => 0,
                'direction' => Manager::DIRECTION_NORTH,
                'cell' => Manager::CELL_BORN,
                'y' => Manager::BORN_LOCATION[0],
                'x' => Manager::BORN_LOCATION[1],
                'items' => [],
                'score' => null,
            ],
            'strategy' => [
                'stageRoutes' => [
                    'end_game',
                    'location',
                    'cancel',
                ],
            ],
        ]);
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    /*--------- exiting --------*/


    /*--------- command --------*/


    /**
     * @param Stage $stage
     * @return Stage
     */
    public function __on_cancel(Stage $stage) : Stage
    {
        return $stage->always(function(Dialog $dialog) {
            return $dialog
                ->send()
                ->info($this->cancelMessage)
                ->over()
                ->fulfill();
        });
    }

    /**
     * 特殊的写法, 为 stage 定义 matcher.
     * @param Matcher $matcher
     * @return bool
     */
    public static function __match_cancel(Matcher $matcher) : bool
    {
        return $matcher
            ->refresh()
            ->is('quit')
            ->is('退出')
            ->truly();
    }

    /**
     * @param Stage $stage
     * @return Stage
     *
     * @spell 作弊
     */
    public function __on_end_game(Stage $stage) : Stage
    {
        $this->score = Manager::makePoints($this->steps);

        return $stage->always(function(Dialog $dialog) {
            return $dialog->send()
                ->info($this->endGameMessage, ['score' => $this->score])
                ->over()
                ->fulfill();
        });
    }

    /**
     * @param Stage $stage
     * @return Stage
     *
     *
     * @title 查询座标
     *
     * @spell 座标
     *
     * @example 座标
     * @example 坐标
     * @example 给我看座标
     * @example 我现在在什么地方
     * @example 这是什么位置
     * @example 我在哪里
     */
    public function __on_location(Stage $stage) : Stage
    {
        return $stage->always(function(Dialog $dialog) {

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
                ->rewind();
        });
    }

    /*--------- method --------*/

    public function fallback(Dialog $dialog) : Operator
    {
        return $dialog
            ->send()
            ->info($this->noticeMessage)
            ->over()
            ->goStage('play');
    }

    /*--------- 正式逻辑 --------*/

    public function __on_start(Stage $stage): Stage
    {
        return $stage->onActivate(function(Dialog $dialog){
            $this->initialize();
            return $dialog
                ->send()
                ->info($this->bornMessage)
                ->over()
                ->goStage('play');
        });

    }


    /**
     * 运行游戏的回合.
     *
     * @param Stage $stage
     * @return Stage
     */
    public function __on_play(Stage $stage) : Stage
    {

        return $stage
            ->onActivate(function(Dialog $dialog) {

                return $dialog
                    ->await()
                    // 并非必要, 只是为了测试.
                    // 其实一个 towardInt 就够了.

                    // 使用这种方式build, 上下文中有以下几种情况可以命中选项:
                    // 1. 命中了 供选择的intent, 例如 MazeFrontInt
                    // 2. 命中了 选项  0, 1, 2, 3
                    // 3. 命中了 意图 TowardsInt
                    // 4. 命中了 意图 ordinalInt, 说第一个, 第二个等等.
                    ->askChoose(
                        $this->thenWhat,
                        [
                            $this->getStage('front'),
                            $this->getStage('right'),
                            $this->getStage('back'),
                            $this->getStage('left'),
                        ]
                    );

            })
            ->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->isVerbal()
                    ->then(function(Dialog $dialog, VerbalMsg $isVerbal) {

                        return $this->parseTowardAndGo($dialog, $isVerbal->getText());
                    })
                    ->end([$this, 'fallback']);
            });
    }

    /*--------- 方向 --------*/

    /**
     * @param Stage $stage
     * @return Stage
     *
     * @title 向左
     * @desc 左
     *
     * @example  向左走
     * @example  往左走
     * @example  走左边
     * @example  左手边走
     *
     */
    public function __on_left(Stage $stage) : Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {
                return $this->goToward($dialog, Manager::TOWARD_LEFT);
            })
            ->onReceive([$this, 'fallback']);
    }

    /**
     * @param Stage $stage
     * @return Stage
     *
     * @title 向右走
     * @desc 右
     *
     * @example 向右走
     * @example 往右走
     * @example 走右边
     * @example 右手边走
     */
    public function __on_right(Stage $stage) : Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {
                return $this->goToward($dialog, Manager::TOWARD_RIGHT);
            })
            ->onReceive([$this, 'fallback']);

    }

    /**
     * @param Stage $stage
     * @return Stage
     *
     * @title 向前
     * @desc 前
     *
     * @example 走正面
     * @example 直走
     * @example 向前走
     * @example 往前走
     * @example 走前边
     * @example 走前头
     */
    public function __on_front(Stage $stage) : Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {
                return $this->goToward($dialog, Manager::TOWARD_FRONT);
            })
            ->onReceive([$this, 'fallback']);;
    }

    /**
     * @param Stage $stage
     * @return Stage
     *
     * @title 向后
     * @desc 后
     *
     * @example 向后走
     * @example 往后走
     * @example 走后边
     * @example 走后头
     */
    public function __on_back(Stage $stage) : Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {
                return $this->goToward($dialog, Manager::TOWARD_BACK);
            })
            ->onReceive([$this, 'fallback']);;
    }

    /*----------------- 游戏逻辑 ----------------*/

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

        $this->merge($props);

        // 游戏次数增加.
        $this->played->total = $this->played->total + 1;
    }

    protected function parseTowardAndGo(Dialog $dialog, string $input) : ? Operator
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


    public function goToward(Dialog $dialog, int $toward) : Operator
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
        $deliver = $dialog->send();
        $deliver->info($first);
        foreach ([$middle, $itemMessage, $last] as $info) {
            if (!empty($info)) {
                $deliver->info($info);
            }
        }

        // 赢了, 游戏结束
        if ($win) {
            return $dialog->goStage('end_game');

            // 走进了另一张门
        } elseif ($success) {

            $this->y = $y;
            $this->x = $x;
            $this->direction = $direction;
            $this->cell = $cell;

            return $dialog->goStage('play');

            // 退了回去
        } else {
            // 面向不改.
            $this->cell = null;
            return $dialog->goStage('play');
        }

    }


    protected function hearToward(int $toward) : \Closure
    {
        return function(Dialog $dialog) use ($toward) {
            return $this->goToward($dialog, $toward);
        };
    }

    /*------------ mutator ---------*/

    public function __get_played() : UserPlayHistory
    {
        return $this->playedHistory
            ?? $this->playedHistory = UserPlayHistory::from($this);
    }


}