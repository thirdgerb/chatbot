<?php


namespace Commune\Demo\App\Contexts;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Demo\App\Cases\Maze\MazeInt;
use Commune\Demo\App\Cases\Wheather\TellWeatherInt;
use Commune\Demo\App\Memories\Sandbox;

/**
 * 用于测试功能的简单 test case
 *
 * @property string $name  请输入您的名字, 测试depend 功能.
 */
class TestCase extends TaskDef
{
    public static function __depend(Depending $depending): void
    {
        $depending->onAnnotations();
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            ->info('您好!'.$this->name)
            ->goStage('menu');
    }

    public function __onMenu(Stage $stage): Navigator
    {
        return $stage->talk(function(Dialog $dialog){
                $dialog->say()
                    ->askVerbose(
                        '请输入:',
                        [
                            0 => 'hello : 测试选项与正则 /^hello/',
                            'test pipe: 测试经过 test pipe, 然后回到start',
                            'sandbox : 测试在config里定义的 memory',
                            'sandbox class: 测试用类定义的 memory',
                            '#tellWeather : 用命令命中意图, 查询天气',
                            5 => 'dependencies: 测试依赖注入参数',
                            6 => '测试 todo -> otherwise api',
                            7 => 'test confirmation with emotion',
                            8 => '迷宫小游戏',
                        ]
                    );
            },
            function(Dialog $dialog, Message $message){

                return $dialog->hear($message)

                    ->isFulfillIntent(TellWeatherInt::class, function(Dialog $dialog) {
                        $dialog->say()->info('命中已完成的天气测试');

                        return $dialog->repeat();
                    })

                    ->runAnyIntent()
                    ->runIntentIn(['Commune.Demo'])

                    ->isChoice(0)
                    ->pregMatch('/^hello/', [])
                    ->then(function(Dialog $dialog, Message $message) : Navigator {
                        $dialog->say()->info('hello.world', ['input' => $message->getText()]);
                        return $dialog->repeat();

                    })

                    ->isChoice(1)
                    ->pregMatch('/^test$/')
                        ->then(function(Dialog $dialog) {
                            $dialog->say()->info('go to test');
                            return $dialog->goStagePipes(['test', 'start']);
                        })


                    ->isChoice(2)
                    ->is('sandbox')
                        ->then(function(Dialog $dialog, Session $session) {
                            $test = $session->memory['sandbox']['test'] ?? 0;
                            $dialog->say()
                                ->info("test is :")
                                ->info($test);

                            $session->memory['sandbox']['test'] = $test + 1;

                            return $dialog->repeat();

                        })
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
                    ->hasKeywords([
                            '测试', ['关键字', 'keyword']
                        ],
                        function (Dialog $dialog) {
                            $dialog->say()->info('命中测试关键字');
                            return $dialog->repeat();
                        }
                    )
                    ->isChoice(4, function(Dialog $dialog){
                        $dialog->say()->info(
                            "请输入 #tellWeather [城市] [时间]"
                        );

                        return $dialog->wait();
                    })
                    ->isChoice(
                        5,
                        function(Dialog $dialog, array $dependencies){

                            $talk = $dialog->say();
                            $talk->info('dependencies are :');
                            foreach ($dependencies as $key => $type) {
                                $talk->info("$key : $type");
                            }
                            return $dialog->restart();
                        }
                    )
                    ->isChoice(6, function(Dialog $dialog){
                        return $dialog->goStage('testTodo');
                    })
                    ->isChoice(7, function(Dialog $dialog){
                        return $dialog->goStage('testConfirmation');
                    })
                    ->isChoice(8, Redirector::goStage('maze'))
                    ->end(function(Dialog $dialog, Message $message){

                        $dialog->say()->info("输入了:" . $message->getText());
                        return $dialog->missMatch();
                    });
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

    public function __exiting(Exiting $listener): void
    {
        $listener->onQuit(function(Dialog $dialog){
            $dialog->say()->info('bye from event');
        });
    }


}