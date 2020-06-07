<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Demo\Contexts;

use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Context\StageBuilder as Stage;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Components\Demo\Recall\Sandbox;
use Commune\Ghost\Context\ACodeContext;
use Commune\Protocals\HostMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @desc demo.contexts.featureTest
 */
class FeatureTest extends ACodeContext
{

    /*------ 定义 ------*/

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            'onCancel' => 'cancel',
            'onQuit' => 'quit',
            'stageRoutes' => [
                'quit',
                'rewind',
                'cancel'
            ],
        ]);
    }


    /*-------- 命令类路由 --------*/

    /**
     * @param StageBuilder $builder
     * @return StageBuilder
     *
     * @spell #q
     */
    public function __on_quit(StageBuilder $builder): StageBuilder
    {
        return $builder->always(function(Dialog $dialog) {
            return $dialog
                ->send()
                ->info('quit from event')
                ->over()
                ->quit();
        });
    }


    /**
     * @param StageBuilder $builder
     * @return StageBuilder
     *
     * @spell #r
     */
    public function __on_rewind(StageBuilder $builder) : StageBuilder
    {
        return $builder->onRedirect(function(Dialog $prev) {
            return $prev->send()->info('rewind')->over()->reactivate();
        });
    }

    /**
     * @param StageBuilder $builder
     * @return StageBuilder
     *
     * @spell #c
     */
    public function __on_cancel(StageBuilder $builder): StageBuilder
    {
        // var_dump(get_class($builder->dialog));
        return $builder->always(function(Dialog $dialog) {
            return $dialog->send()
                ->info('cancel from event')
                ->over()
                ->cancel();
        });
    }

    /*-------- 功能测试用例 --------*/

    /*------ 管道 ------*/

    public function __on_test_pipe_start(Stage $stage) : Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog) use ($stage){
                return $dialog
                    ->send()
                    ->info(
                        "进入管道 {pipe}, 输入任何信息继续",
                        ['pipe' => $stage->def->getName()]
                    )
                    ->over()
                    ->await();
            })
            ->onReceive(function(Dialog $dialog){
                return $dialog
                    ->send()
                    ->info("进入下一步")
                    ->over()
                    ->next();
            });
    }

    /*------ 启动 ------*/

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage->onActivate(function(Dialog $dialog){
            return $dialog
                ->await()
                ->askChoose(
                    '请选择功能测试用例 (输入 #q 退出测试, #r 回到选项, #c 退出语境)',
                    [
                        $this->getStage('test_match'),
                        $this->getStage('test_memory'),
                        'confirmation && emotion' => 'test_confirmation',
                        'askContinue 机制' => 'test_ask_continue',
                        'gc 机制' => 'test_gc',
                        'stage exiting 事件' => 'test_exiting',
                    ]
                );
        });
    }


    /**
     * 测试匹配逻辑.
     * @param Stage $stage
     * @return Stage
     *
     * @title testMatch
     * @desc 常用匹配逻辑
     */
    public function __on_test_match(Stage $stage): Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog) : Operator {
                return $dialog
                    ->send()
                    ->info(
                        <<<EOF
可测试的内容:
-   /^hello/ 正则匹配.
-   /^test$/ 正则匹配并尝试经过管道.
-   /depend/ 查看闭包的 dependencies 参数
-   [测试, [关键字,keyword]] 关键字匹配
-   ordinalInt 正则匹配.
EOF

                    )
                    ->over()
                    ->await();
            })
            ->onReceive(function(Dialog $dialog) : Operator {

                return $dialog
                    ->hearing()

                    // hello
                    ->todo(function(Dialog $dialog, HostMsg $message) {
                        $dialog->send()->info(
                            'hello.world',
                            ['input' => $message->getText()]
                        );

                        return $dialog->reactivate();
                    })
                    ->pregMatch('/^hello/')

                    // test
                    ->todo(function(Dialog $dialog) {

                        return $dialog
                            ->send()
                            ->info('go to testPipeStart stage')
                            ->over()
                            ->goStage('test_pipe_start', 'test_match');
                    })
                    ->pregMatch('/^test$/')

                    ->then()

                    // 适用 第n个 这种形式, 匹配
//                    ->isIntent(OrdinalInt::class)
//                    ->then(function(OrdinalInt $int, Dialog $dialog){
//                        $dialog->send()->info('匹配到了%ord%', [
//                            'ord' => implode(',', $int->ordinal)
//                        ]);

//                        return $dialog->redirect()->reactivate();

//                    })

                    // keyword
                    ->hasKeywords(['测试', ['关键字', 'keyword']])
                    ->then(function (Dialog $dialog) {
                        return $dialog->send()
                            ->info('命中测试关键字')
                            ->over()
                            ->reactivate();
                    })

                    // depend
                    ->todo(function(Dialog $dialog, array $dependencies){

                        $typer = $dialog
                            ->send()
                            ->info('dependencies are :');

                        foreach ($dependencies as $key => $type) {
                            $typer->info("$key : $type");
                        }

                        return $dialog->reactivate();
                    })
                    ->pregMatch('/depend/')
                    ->end();
                }
            );
    }


    /**
     * @param Stage $stage
     * @return Stage
     *
     * @desc 上下文记忆
     */
    public function __on_test_memory(Stage $stage): Stage
    {
        return $stage
            ->onActivate(function(Dialog $dialog) : Operator {

                return $dialog
                    ->await()
                    ->askChoose(
                        '测试记忆功能',
                        [
                            'a' => 'sandbox : 测试在config里定义的 memory',
                            'b' => 'sandbox class: 测试用类定义的 memory',
                            '返回',
                        ]
                    );

            })
            ->onReceive(function(Dialog $dialog) : Operator {
                return $dialog
                    ->hearing()
                    ->todo($dialog->goStage(''))
                        ->isChoice(0)
                    ->todo(function(Dialog $dialog) {

                        $sandbox = $dialog->recall(Sandbox::class);
                        $test = $sandbox['test'];

                        $dialog
                            ->send()
                            ->info(
                                "test is : {test}",
                                ['test' => $test]
                            );

                        $sandbox['test'] = $test + 1;

                        return $dialog->reactivate();

                    })
                    ->isChoice('a')
                    ->is('sandbox')

                    ->then()

                    ->isChoice('b')
                    ->then(function(Dialog $dialog){

                        $s = Sandbox::find($dialog->cloner);
                        $test = $s->test ?? 0;
                        $test1 = $s->test1 ?? 0;
                        $s->test = $test + 1;
                        $s->test1 = $test1 + 2;

                        $dialog->send()
                            ->withSlots($s->toArray())
                            ->info(
                                'class '
                                . Sandbox::class
                                . ' value is test:{test}, test1:{test1}'
                            );

                        return $dialog->reactivate();
                    })
                    ->end();
            }
        );
    }


}