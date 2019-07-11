<?php


namespace Commune\Demo\App\Contexts;


use Commune\Chatbot\App\Components\SimpleChat\Callables\SimpleChatAction;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Session;
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
        return $stage->talk(function(Dialog $dialog){
                $dialog->say()
                    ->info('您好!'.$this->name)
                    ->askVerbose(
                        '请输入:',
                        [
                            0 => 'hello : 测试选项与正则 /^hello/',
                            'test pipe: 测试经过 test pipe, 然后回到start',
                            'sandbox : 测试在config里定义的 memory',
                            'sandbox class: 测试用类定义的 memory',
                            '#tellWeather : 用命令命中意图, 查询天气',
                            5 => 'dependencies: 测试依赖注入参数'
                        ]
                    );
            },
            function(Dialog $dialog, Message $message){

                return $dialog->hear($message)
                    ->isAnyIntent()
                    ->isIntentIn(['Commune.Demo'])
                    ->isChoice(0)
                    ->pregMatch('/^hello/', [])
                    ->heard(function(Dialog $dialog) : Navigator {
                        $dialog->say()->info('hello world!');
                        return $dialog->repeat();

                    })
                    ->isChoice(1)
                    ->pregMatch('/^test$/')
                        ->heard(function(Dialog $dialog) {
                            $dialog->say()->info('go to test');
                            return $dialog->goStagePipes(['test', 'start']);
                        })

                    ->isChoice(2)
                    ->is('sandbox')
                        ->heard(function(Dialog $dialog, Session $session) {
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
                            "请输入 #tellWeather [城市] [天气]"
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
                    ->interceptor(new SimpleChatAction('example'))
                    ->end(function(Dialog $dialog, Message $message){

                        $dialog->say()->info("输入了:" . $message->getText());
                        return $dialog->missMatch();
                    });
            });
    }

    public function __onTest(Stage $stage): Navigator
    {
        return $stage->onStart(function(Dialog $dialog) {
            $dialog->say()
                ->info('test stage start')
                ->askVerbose('test ');
            return $dialog->wait();

        })->wait(function(Dialog $dialog, Message $message) {

            $dialog->say()->info('您输入的是:'.$message->getText());
            return $dialog->next();
        });

    }


    public function __exiting(Exiting $listener): void
    {
        $listener->onQuit(function(Dialog $dialog){
            $dialog->say()->info('bye from event');
        });
    }


}