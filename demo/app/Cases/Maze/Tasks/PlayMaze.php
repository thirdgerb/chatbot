<?php

/**
 * Class PlayMaze
 * @package Commune\Demo\App\Cases\Maze\Tasks
 */

namespace Commune\Demo\App\Cases\Maze\Tasks;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Demo\App\Cases\Maze\Intents\LocationInt;
use Commune\Demo\App\Cases\Maze\Logic\Manager;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Demo\App\Cases\Maze\Intents\MazeBackInt;
use Commune\Demo\App\Cases\Maze\Intents\MazeFrontInt;
use Commune\Demo\App\Cases\Maze\Intents\MazeLeftInt;
use Commune\Demo\App\Cases\Maze\Intents\MazeRightInt;
use Commune\Demo\App\Cases\Maze\Intents\TowardsInt;
use Commune\Demo\App\Cases\Maze\Memories\UserPlayHistory;


/**
 * 进行迷宫
 *
 * @property-read  UserPlayHistory $played
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
class PlayMaze extends TaskDef
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

    /*-------------- 默认方法 ---------------*/

    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
        $listener->onCancel(function(Dialog $dialog) {
            $dialog->say()->info($this->cancelMessage);
        });
    }


    /**
     * 全局 hearing
     * @param Hearing $hearing
     */
    public function __hearing(Hearing $hearing)
    {
        $hearing
            ->todo(Redirector::goStage('endGame'))
                ->is('作弊')

            // 听到 '退出' 时 退出.
            ->todo(Redirector::goCancel())
                ->is('退出')
                ->is('quit')

            // 听到 '坐标' 时 查看坐标.
            ->todo(function(Dialog $dialog) {
                $dialog->say()->info($this->locationMessage, [
                    'x' => $this->x + 1,
                    'y' => 5 - $this->y,
                    'direction' => $this->directions[$this->direction]
                ]);


                return $dialog->repeat();

            })
                ->is('坐标')
                ->isIntent(LocationInt::class)
            ->otherwise()

            // 注册默认 end 方法.
            ->defaultFallback(function(Dialog $dialog){
                $dialog->say()->info($this->noticeMessage);
                return $dialog->wait();
            });
    }


    /*-------------- 执行逻辑 ---------------*/


    /**
     * 游戏开始.
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onStart(Stage $stage): Navigator
    {
        $this->initialize();
        return $stage->buildTalk()
            ->info($this->bornMessage)
            ->goStage('play');

    }


    /**
     * 运行游戏的回合.
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onPlay(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            // 并非必要, 只是为了测试.
            // 其实一个 towardInt 就够了.

            // 使用这种方式build, 上下文中有以下几种情况可以命中选项:
            // 1. 命中了 供选择的intent, 例如 MazeFrontInt
            // 2. 命中了 选项  0, 1, 2, 3
            // 3. 命中了 意图 TowardsInt
            // 4. 命中了 意图 ordinalInt, 说第一个, 第二个等等.
            ->askChooseIntents(
                $this->thenWhat,
                [
                    '前',
                    '后',
                    '左',
                    '右',
                ],
                [
                    MazeFrontInt::getContextName(),
                    MazeBackInt::getContextName(),
                    MazeLeftInt::getContextName(),
                    MazeRightInt::getContextName(),

                ]
            )
            ->hearing()

            ->isIntent(TowardsInt::class, function(Dialog $dialog, TowardsInt $towards) : ? Navigator{
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

            ->otherwise()
            ->isInstanceOf(VerboseMsg::class, function(Message $message, Dialog $dialog){
                return $this->parseTowardAndGo($dialog, $message->getText());
            })

            ->end();
    }



    public function __onEndGame(Stage $stage) : Navigator
    {
        $this->score = Manager::makePoints($this->steps);
        return $stage->buildTalk()
            ->info($this->endGameMessage, ['score' => $this->score])
            ->fulfill();
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

        // 游戏次数增加.
        $this->played->total += 1;
        $this->fillProperties($props);
    }

    protected function parseTowardAndGo(Dialog $dialog, string $input) : ? Navigator
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


    public function goToward(Dialog $dialog, int $toward) : Navigator
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
        $speech = $dialog->say();
        $speech->info($first);
        $paragraph = $speech->beginParagraph();
        foreach ([$middle, $itemMessage, $last] as $info) {
            if (!empty($info)) {
                $paragraph->info($info);
            }
        }
        $paragraph->endParagraph();

        // 赢了, 游戏结束
        if ($win) {
            return $dialog->goStage('endGame');

            // 走进了另一张门
        } elseif ($success) {
            $this->y = $y;
            $this->x = $x;
            $this->direction = $direction;
            $this->cell = $cell;

            return $dialog->repeat();

            // 退了回去
        } else {
            // 面向不改.
            $this->cell = null; // 下一次一定介绍.
            return $dialog->repeat();
        }

    }


    protected function hearToward(int $toward) : \Closure
    {
        return function(Dialog $dialog) use ($toward) {
            return $this->goToward($dialog, $toward);
        };
    }

    /*------------ mutator ---------*/

    public function __getPlayed() : UserPlayHistory
    {
        return $this->playedHistory
            ?? $this->playedHistory = UserPlayHistory::from($this);
    }


}