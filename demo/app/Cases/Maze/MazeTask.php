<?php


namespace Commune\Demo\App\Cases\Maze;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Demo\App\Cases\Maze\Intents\LocationInt;
use Commune\Demo\App\Cases\Maze\Intents\TowardBackInt;
use Commune\Demo\App\Cases\Maze\Intents\TowardFrontInt;
use Commune\Demo\App\Cases\Maze\Intents\TowardLeftInt;
use Commune\Demo\App\Cases\Maze\Intents\TowardRightInt;
use Commune\Demo\App\Cases\Maze\Logic\Manager;

/**
 * @property-read int $steps
 * @property-read int $direction
 * @property-read string $cell
 * @property-read int $y
 * @property-read int $x
 * @property-read string[][] $map
 * @property-read string[] $items
 * @property-read null|int $score
 */
class MazeTask extends TaskDef
{
    const DESCRIPTION = '迷宫小游戏';


    protected $towardMessages = [
        Manager::TOWARD_FRONT => 'demo.maze.toward.front',
        Manager::TOWARD_LEFT => 'demo.maze.toward.left',
        Manager::TOWARD_RIGHT => 'demo.maze.toward.right',
        Manager::TOWARD_BACK => 'demo.maze.toward.back',
    ];

    protected $oneMore = 'demo.maze.dialog.oneMore';

    protected $locationMessage = 'demo.maze.info.location';

    protected $bornMessage = 'demo.maze.info.born';

    protected $failToEnter = 'demo.maze.actions.back';

    protected $falwellMessage = 'demo.maze.info.falwell';

    protected $cancelMessage = 'demo.maze.info.cancel';

    protected $thenWhat = 'demo.maze.dialog.then';

    protected $incomprehension = 'demo.maze.info.incomprehension';

    protected $sameRoomMessage = 'demo.maze.info.sameRoom';

    protected $welcome = 'demo.maze.info.welcome';

    protected $wantIntro = 'demo.maze.dialog.wantIntro';

    protected $introduce = 'demo.maze.info.introduce';

    protected $confirmStart = 'demo.maze.dialog.start';

    protected $winMessage = 'demo.maze.info.win';

    protected $endGameMessage = 'demo.maze.info.end';

    protected $noticeMessage = 'demo.maze.info.notice';

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
            '这个房间里乌漆嘛黑, 什么也看不见',
            '火把的光芒着照亮了黑暗的房间',
        ],
        Manager::CELL_ICE => [
            '房间地面结着厚厚的冰, 根本无法落脚',
            '鞋子踩在房间的冰面上, 发出嘎吱嘎吱的响声',
        ],
        Manager::CELL_WALL => [
            '这个房间三面都是墙壁, 没有任何路可走',
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

    public function __construct()
    {
        parent::__construct([]);
    }


    public function __hearing(Hearing $hearing)
    {
        $hearing
            ->todo(Redirector::goCancel())
                ->is('退出')
                ->is('quit')
            ->todo(function(Dialog $dialog) {
                $dialog->say()->info($this->locationMessage, [
                    'x' => $this->x + 1,
                    'y' => $this->y + 1,
                    'direction' => $this->directions[$this->direction]
                ]);


                return $dialog->repeat();

            })
                ->is('坐标')
                ->isIntent(LocationInt::class)
            ->otherwise();;
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            ->info($this->welcome)
            ->askConfirm($this->wantIntro)
            ->hearing()
            ->isPositive(Redirector::goStage('intro'))
            ->isNegative(Redirector::goStage('born'))
            ->end(function(Dialog $dialog){
                return $dialog->cancel();
            });
    }

    public function __onIntro(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->info($this->introduce)
            ->askConfirm($this->confirmStart)
            ->hearing()
            ->isPositive(Redirector::goStage('born'))
            ->isNegative(Redirector::goFulfill())
            ->end(function(Dialog $dialog){
                return $dialog->cancel();
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
        $this->fillProperties($props);
    }

    public function __onBorn(Stage $stage) : Navigator
    {
        $this->initialize();
        return $stage->buildTalk()
            ->info($this->bornMessage)
            ->goStage('play');

    }

    public function __onPlay(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askChooseIntents(
                $this->thenWhat,
                [
                    '前',
                    '后',
                    '左',
                    '右',
                ],
                [
                    TowardFrontInt::getContextName(),
                    TowardBackInt::getContextName(),
                    TowardLeftInt::getContextName(),
                    TowardRightInt::getContextName(),
                ]
            )
            ->hearing()
            ->todo($this->doToward(Manager::TOWARD_FRONT))
                ->isIntent(TowardFrontInt::class)
                ->isChoice(0)

            ->todo($this->doToward(Manager::TOWARD_BACK))
                ->isIntent(TowardBackInt::class)
                ->isChoice(1)

            ->todo($this->doToward(Manager::TOWARD_LEFT))
                ->isIntent(TowardLeftInt::class)
                ->isChoice(2)

            ->todo($this->doToward(Manager::TOWARD_RIGHT))
                ->isIntent(TowardRightInt::class)
                ->isChoice(3)

            ->end(function(Dialog $dialog){
                $dialog->say()
                    ->warning($this->noticeMessage);
                return $dialog->wait();
            });
    }

    protected function doToward(int $toward) : \Closure
    {
        return function(Dialog $dialog) use ($toward) {
            return $this->goToward($dialog, $toward);
        };
    }

    public function __onEndGame(Stage $stage) : Navigator
    {
        $this->score = Manager::makePoints($this->steps);
        return $stage->buildTalk()
            ->info($this->endGameMessage, ['score' => $this->score])
            ->askConfirm($this->oneMore)
            ->hearing()
                ->isPositive(Redirector::goStage('born'))
                ->isNegative(function(Dialog $dialog) {
                    $dialog->say()->info($this->falwellMessage);
                    return $dialog->fulfill();
                })
                ->end(Redirector::goFulfill());
    }

    public function __exiting(Exiting $listener): void
    {
        $cancel = function(Dialog $dialog) {
            if (isset($this->score) || !isset($this->map)) {
                $dialog->say()->info($this->falwellMessage);
            } else {

                $dialog->say()->info($this->cancelMessage);
            }
            return null;
        };

        $listener->onCancel($cancel);
    }


    public static function __depend(Depending $depending): void
    {
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
        foreach ([$first, $middle, $itemMessage, $last] as $info) {
            if (!empty($info)) {
                $speech->info($info);
            }
        }

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

}