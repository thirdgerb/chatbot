<?php


namespace Commune\Demo\App\Contexts;


use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Session\Session;
use Commune\Demo\App\Components\CustomService\Routes\HearUserCall;
use Commune\Demo\App\Intents\TestInt;
use Commune\Demo\App\Memories\Sandbox;

/**
 * @property string $name
 */
class Welcome extends TaskDef
{
    public static function __depend(Depending $depending): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->talk(function(Dialog $dialog){
                $dialog->say()
                    ->info('你好!'.$this->name)
                    ->askVerbose(
                        '请输入:',
                        [
                            0 => 'hello',
                            'test',
                            'memory',
                            'test class memory',
                            '测试客服',
                            5 => '测试依赖注入参数'
                        ]
                    );
            },
            function(Dialog $dialog, Message $message){

                return $dialog->hear($message)
                    ->isIntentIn(
                        ['demo'],
                        function(Dialog $dialog, TestInt $message){
                            $dialog->say()
                                ->info("matched test intent");
                            return $dialog->repeat();
                        }
                    )
                    ->isAnyIntent()
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
                        return $dialog->goStage('testCustom');

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
                    ->end(function(Dialog $dialog, Message $message){

                        $dialog->say()->info("输入了:" . $message->getText());
                        return $dialog->missMatch();
                    });
            });
    }

    public function __onTestCustom(Stage $stage) : Navigator
    {
        return $stage->sleepTo(new HearUserCall(), function(Dialog $dialog){
            $dialog->say()->info("完成客服测试.");
            return $dialog->restart();
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