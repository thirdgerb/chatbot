<?php


namespace Commune\Demo\App\Contexts;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Demo\App\Cases\Maze\MazeInt;
use Commune\Demo\App\Cases\Wheather\TellWeatherInt;
use Commune\Demo\App\Memories\Sandbox;
use Commune\Demo\App\Memories\UserInfo;

/**
 * @property-read UserInfo $userInfo
 */
class TestCase extends TaskDef
{
    public static function __depend(Depending $depending): void
    {
        $depending->onMemory('userInfo', UserInfo::class);
    }


    public function __exiting(Exiting $listener): void
    {
        $listener
            ->onQuit(function(Dialog $dialog){
                $dialog->say()->info('bye from event');
            })
            ->onCancel(Redirector::goQuit());
    }

    public function __hearing(Hearing $hearing) : void
    {
        $hearing->fallback(function(Dialog $dialog, Message $message){
            $dialog->say()->info("输入了:" . $message->getText());
            return $dialog->missMatch();
        });
    }



    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            ->info('您好!'.$this->userInfo->name)
            ->goStage('menu');
    }

    public function __onFeatureTest(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askVerbose(
                '请选择功能测试用例',
                [
                    0 => 'hello : 测试选项与正则 /^hello/',
                    'test pipe: 测试经过 test pipe, 然后回到start',
                    'sandbox : 测试在config里定义的 memory',
                    'sandbox class: 测试用类定义的 memory',
                    4 => 'dependencies: 测试依赖注入参数',
                    5 => '测试 todo -> otherwise api',
                    6 => 'test confirmation with emotion',
                    7 => '返回菜单',
                ]
            )
            ->hearing()
                ->todo(function(Dialog $dialog, Message $message) : Navigator {
                    $dialog->say()->info('hello.world', ['input' => $message->getText()]);
                    return $dialog->repeat();

                })
                    ->isChoice(0)
                    ->pregMatch('/^hello/', [])

                ->todo(function(Dialog $dialog) {
                    $dialog->say()->info('go to test');
                    return $dialog->goStagePipes(['test', 'start']);
                })
                    ->isChoice(1)
                    ->pregMatch('/^test$/')

                ->todo(function(Dialog $dialog, Session $session) {
                    $test = $session->memory['sandbox']['test'] ?? 0;
                    $dialog->say()
                        ->info("test is :")
                        ->info($test);

                    $session->memory['sandbox']['test'] = $test + 1;

                    return $dialog->repeat();

                })
                    ->isChoice(2)
                    ->is('sandbox')

                ->otherwise()
                ->isChoice(3, function(Dialog $dialog){

                    $s = Sandbox::from($this);
                    $test = $s->test ?? 0;
                    $test1 = $s->test1 ?? 0;
                    $s->test = $test + 1;
                    $s->test1 = $test1 + 1;

                    $dialog->say()
                        ->withContext($s, ['test', 'test1'])
                        ->info(
                            'class '
                            . Sandbox::class
                            . ' value is test:%test%, test1:%test1%'
                        );

                    return $dialog->repeat();
                })

                ->isChoice(
                    4,
                    function(Dialog $dialog, array $dependencies){

                        $talk = $dialog->say();
                        $talk->info('dependencies are :');
                        foreach ($dependencies as $key => $type) {
                            $talk->info("$key : $type");
                        }
                        return $dialog->repeat();
                    }
                )

                ->isChoice(5, function(Dialog $dialog){
                    return $dialog->goStage('testTodo');
                })
                ->isChoice(6, function(Dialog $dialog){
                    return $dialog->goStage('testConfirmation');
                })

                ->isChoice(7, Redirector::goStage('menu'))

                ->hasKeywords([
                    '测试', ['关键字', 'keyword']
                ],
                    function (Dialog $dialog) {
                        $dialog->say()->info('命中测试关键字');
                        return $dialog->repeat();
                    }
                )

            ->end();

    }

    public function __onMenu(Stage $stage): Navigator
    {
        return $stage->talk(function(Dialog $dialog){

                $dialog->say()
                    ->askVerbose(
                        '请输入:',
                        [
                            '功能点测试',
                            '#tellWeather : 用命令命中意图, 查询天气',
                            8 => '迷宫小游戏',
                            9 => '测试子会话',
                        ]
                    );

                return $dialog->wait();
            },
            function(Dialog $dialog, Message $message){

                return $dialog->hear($message)
                    ->isFulfillIntent(TellWeatherInt::class, function(Dialog $dialog) {
                        $dialog->say()->info('命中已完成的天气测试');

                        return $dialog->repeat();
                    })
                    ->runAnyIntent()
                    ->runIntentIn(['Commune.Demo'])

                    ->isChoice(0, Redirector::goStage('featureTest'))

                    ->isChoice(4, function(Dialog $dialog){
                        $dialog->say()->info(
                            "请输入 #tellWeather [城市] [时间]"
                        );

                        return $dialog->wait();
                    })

                    ->isChoice(8, Redirector::goStage('maze'))

                    ->isChoice(9, Redirector::goStage('subDialog'))

                    ->end();
            });
    }

    public function __onMaze(Stage $stage) : Navigator
    {
        return $stage->dependOn(MazeInt::class, function(Dialog $dialog){
            $dialog->say()->info('迷宫小游戏退出');
            return $dialog->goStage('menu');
        });
    }

    public function __onTest(Stage $stage): Navigator
    {
        return $stage->onStart(function(Dialog $dialog) {
            $dialog->say()
                ->info('test stage start')
                ->askVerbose('输入一个值 (会展示这个值然后跳到下一步)');
            return $dialog->wait();

        })->wait(function(Dialog $dialog, Message $message) {

            $dialog->say()->info('您输入的是:'.$message->getText());
            return $dialog->next();
        });

    }

    /**
     * test to do api and test define stage by annotation
     *
     * @stage 用 annotation 方式定义这个 stage
     * @param Stage $stage
     * @return Navigator
     */
    public function testTodo(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->info('测试 todo api. 请输入 123, 456, 789 测试(quit 退出):')
            ->wait()
            ->hearing()

                ->todo(function(Dialog $dialog, Message $message){
                    $dialog->say()->info('matched case1 ' . $message->getText());
                    return $dialog->wait();
                })
                    ->is('123')
                    ->is('456')
                    ->soundLike('一二三四')
                    ->soundLikePart('二三四五')

                ->todo(function(Dialog $dialog){

                    $dialog->say()->info('matched case2 789');
                    return $dialog->wait();
                })
                    ->pregMatch('/^789/')

                ->todo(function(Dialog $dialog){
                    return $dialog->goStage('menu');
                })
                    ->is('quit')

                ->end();

    }

    public function __onTestConfirmation(Stage $stage) : Navigator
    {
        $result = $stage->buildTalk();

        $result = $result
            ->askConfirm('try to confirm this. test positive emotion. ')
            ->wait();

        $result = $result->hearing()
                ->isPositive(function(Dialog $dialog){
                    $dialog->say()->info('is positive emotion');
                    return $dialog->goStage('menu');
                })
                ->isNegative(function(Dialog $dialog){
                    $dialog->say()->info('is negative emotion');
                    return $dialog->goStage('menu');
                })
                ->end(function(Dialog $dialog){
                    $dialog->say()->warning('nether yes nor no');
                    return $dialog->goStage('menu');
                });

        return $result;
    }


    /**
     * 测试子会话的各种功能.
     * 可以测试的点:
     *
     *
     * 1. before : 父dialog 拦截到, 仍然进入子dialog
     * 2. stop  : 父dialog 拦截到, 不进入子dialog
     * 3. miss : 子dialog miss, 父dialog 返回拦截到
     * 4. quit : 子dialog quit, 父dialog 拦截到, 返回菜单.
     * 5. fulfill : 子dialog fulfill, 触发 quit
     * 6. next : 子dialog 切换stage, 直到触发 fulfill
     * 7. maze : 子dialog 进入迷宫游戏
     * 8. stage : 查看子dialog 的stage
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onSubDialog(Stage $stage) : Navigator
    {
        return $stage
            ->onStart(function(Dialog $dialog){
                $dialog->say()->info('sub dialog start');
            })
            ->onCallback(function(Dialog $dialog){
                $dialog->say()->info('sub dialog callback');
            })
            ->onFallback(function(Dialog $dialog){
                $dialog->say()->info('sub dialog fallback');
            })
            ->onSubDialog(
                    $this->getId(),
                    function(){
                        return new SubDialogCase();
                    }
                )
                ->onBefore(function(Dialog $dialog){
                    return $dialog->hear()
                        ->is('before', function(Dialog $dialog){
                            $dialog->say()->info('hit before');
                            return null;
                        })
                        ->is('stop', function(Dialog $dialog) {
                            $dialog->say()->info('stop sub dialog');
                            return $dialog->wait();
                        })
                        ->heardOrMiss();
                })
                ->onWait(function(Dialog $dialog){
                    $dialog->say()->info('sub dialog is wait');
                    return $dialog->wait();
                })
                ->onMiss(function(Dialog $dialog){
                    $dialog->say()->info('sub dialog miss match');
                    return $dialog->hear()
                        ->is('miss', function(Dialog $dialog){
                            $dialog->say()->info('catch miss');
                            return $dialog->wait();
                        })
                        ->end();
                })
                ->onQuit(function(Dialog $dialog){
                    $dialog->say()->info('sub dialog want quit');
                    return $dialog->goStage('menu');
                })
                ->end();


    }
}