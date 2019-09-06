<?php


namespace Commune\Demo\App\Cases\Maze;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\VerboseMsg;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Hearing;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Demo\App\Cases\Maze\Logic\Manager;

/**
 * @property-read int $steps
 * @property-read int $direction
 * @property-read string $cell
 * @property-read int $y
 * @property-read int $x
 * @property-read string|null $toward
 * @property-read string[][] $map
 * @property-read string[] $items
 * @property-read null|int $score
 */
class MazeTask extends TaskDef
{
    const DESCRIPTION = '迷宫小游戏';


    protected $towardMessages = [
        Manager::TOWARD_FRONT => '推开了面前的门',
        Manager::TOWARD_LEFT => '推开了左手边的门',
        Manager::TOWARD_RIGHT => '推开了右手边的门',
        Manager::TOWARD_BACK => '退回了背后的门',
    ];

    protected $oneMore = '还想再来一局吗?';

    protected $locationMessage = "当前所在房间编号是横坐标%x%, 纵坐标%y%, 面朝%direction%";

    protected $bornMessage = '游戏开始! 
我被传送到了一个神秘的迷宫, 自己赤手空拳还光着脚.  
房间里只有一个锁住的升降电梯, 什么别的都没有. 看来要拿到钥匙才能出去. 
前,后,左,右四面墙的中央各有一张可以打开的门.';

    protected $failToEnter = '我赶紧退了回去';

    protected $falwellMessage = '再见. 欢迎再来!';

    protected $cancelMessage = '告诉您一个秘诀, 可以说"坐标"知道自己位置哦. 再见! 希望下次再挑战.';

    protected $thenWhat = '接下来要往哪个方向走呢';

    protected $quitMessage = '对不起, 没有明白您的意思, 游戏退出';

    protected $sameRoomMessage = '进入了同样的房间';

    protected $welcome = '欢迎来到迷宫小游戏!';

    protected $wantIntro = '您要听游戏介绍吗?';

    protected $introduce = '这是一个由25个房间组成的小迷宫, 每个房间都是正方形的, 四个方向有四张门.
在游戏里您只能发出指令说: 向前, 向后, 向左, 向右. 控制角色前进.
任何时候说 "退出" 则会退出游戏. ';

    protected $confirmStart = '您要开始这个游戏吗?';

    protected $winMessage = '我用找到的钥匙打开了电梯, 惴惴不安地走了进去.';

    protected $endGameMessage = '游戏结束! 您的得分是%score%. 恭喜您获得胜利!';

    protected $noticeMessage = '没能明白您的意思. 您可以说"往前", "往后", "往左", "往右"来进入下一个房间, 或者说"退出"以退出游戏';

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
            ->is('退出', function(Dialog $dialog){
                return $dialog->fulfill();
            })->is('坐标', function(Dialog $dialog) {


                $dialog->say()->info($this->locationMessage, [
                    'x' => $this->x + 1,
                    'y' => $this->y + 1,
                    'direction' => $this->directions[$this->direction]
                ]);


                return $dialog->repeat();

            });
        ;
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
            ->askVerbose($this->thenWhat)
            ->hearing()
            ->expect(
                function(Message $message){
                    if (!$message instanceof VerboseMsg) {
                        return false;
                    }

                    $text = $message->getText();

                    $keys = [
                        '前' => Manager::TOWARD_FRONT,
                        'f' => Manager::TOWARD_FRONT,
                        '后' => Manager::TOWARD_BACK,
                        'b' => Manager::TOWARD_BACK,
                        '退' => Manager::TOWARD_BACK,
                        '左' => Manager::TOWARD_LEFT,
                        'l' => Manager::TOWARD_LEFT,
                        '右' => Manager::TOWARD_RIGHT,
                        'r' => Manager::TOWARD_RIGHT,
                    ];

                    foreach ($keys as $key => $toward) {
                        if (strpos($text, $key) !== false) {
                            $this->toward = $toward;
                            break;
                        }
                    }

                    return isset($this->toward);

                }, function(Dialog $dialog){
                    $toward = $this->toward;
                    $this->toward = null;
                    return $this->goToward($dialog, $toward);
                }
            )
            ->end(function(Dialog $dialog){
                $dialog->say()
                    ->warning($this->noticeMessage);

                return $dialog->wait();
            });
    }

    public function __onEndGame(Stage $stage) : Navigator
    {
        $this->score = Manager::makePoints($this->steps);
        return $stage->buildTalk()
            ->info($this->endGameMessage, ['score' => $this->score])
            ->askConfirm($this->oneMore)
            ->hearing()
                ->isPositive(Redirector::goStage('born'))
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

        $output = "$first.$middle.$itemMessage.$last";
        $dialog->say()->info($output);

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
            $this->cell = null; // 下一次一定介绍.
            return $dialog->repeat();
        }

    }

}