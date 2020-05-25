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

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Dialog\Exiting;
use Commune\Blueprint\Ghost\MindDef\StageDef;
use Commune\Blueprint\Ghost\Operate\Hearing;
use Commune\Components\Demo\Recall\Sandbox;
use Commune\Host\Contexts\ACodeContext;
use Commune\Host\Contexts\CodeContext\BuildHearing;
use Commune\Host\Contexts\CodeContext\OnWithdraw;
use Commune\Blueprint\Ghost\Context\StageBuilder as Stage;
use Commune\Protocals\HostMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class FeatureTest extends ACodeContext implements
    BuildHearing,
    OnWithdraw
{

    const DESCRIPTION = 'demo.contexts.featureTest';

    public function __hearing(Hearing $hearing) : Hearing
    {
        return $hearing
            ->todo($hearing->nav()->quit())
                ->is('#q')
            ->todo($hearing->nav()->reactivate())
                ->is('#r')
            ->then();
    }

    public function __withdraw(Exiting $dialog): ? Dialog
    {
        if ($dialog->isEvent(Dialog::CANCEL)) {
            $dialog->send()->info('cancel from '. __METHOD__);

        } elseif($dialog->isEvent(Dialog::QUIT)) {
            $dialog->send()->info('quit from '. __METHOD__);
        }

        return null;
    }

    public function __on_start(Stage $stage): StageDef
    {
        return $stage->onActivate(function(Dialog $dialog) : Dialog {

            return $dialog
                ->await()
                ->askChoose(
                    '请选择功能测试用例 (输入 #q 退出测试, #r 回到选项)',
                    [
                        '常用匹配逻辑' => 'test_match',
                        '上下文记忆' => 'test_memory',
                        'confirmation && emotion' => 'test_confirmation',
                        'askContinue 机制' => 'test_ask_continue',
                        'gc 机制' => 'test_gc',
                        'stage exiting 事件' => 'test_exiting',
                    ]
                );

        })
        ->end();
    }


    /**
     * 测试匹配逻辑.
     * @param Stage $stage
     * @return StageDef
     */
    public function __on_test_match(Stage $stage): StageDef
    {
        return $stage
            ->onActivate(function(Dialog $dialog) : Dialog {
                $dialog->send()
                    ->info(
                        <<<EOF
可测试的内容:
-   /^hello/ 正则匹配.
-   /^test$/ 正则匹配并尝试经过管道.
-   /depend/ 查看闭包的 dependencies 参数
-   [测试, [关键字,keyword]] 关键字匹配
-   ordinalInt 正则匹配.
EOF

                    );
                return $dialog->await();
            })
            ->onEvent(
                Dialog::HEED,
                function(Dialog $dialog) : Dialog {

                    return $dialog
                        ->hearing()

                        // hello
                        ->todo(function(Dialog $dialog, HostMsg $message) {
                            $dialog->send()->info(
                                'hello.world',
                                ['input' => $message->getText()]
                            );

                            return $dialog->redirect()->reactivate();
                        })
                        ->pregMatch('/^hello/')

                        // test
                        ->todo(function(Dialog $dialog) {

                            $dialog->send()->info('go to testPipeStart stage');
                            return $dialog
                                ->redirect()
                                ->next('testPipeStart', 'testMatch');
                        })
                        ->pregMatch('/^test$/')

                        ->then()

                        // 适用 第n个 这种形式, 匹配
                        ->isIntent(OrdinalInt::class)
                        ->then(function(OrdinalInt $int, Dialog $dialog){
                            $dialog->send()->info('匹配到了%ord%', [
                                'ord' => implode(',', $int->ordinal)
                            ]);

                            return $dialog->redirect()->reactivate();

                        })

                        // keyword
                        ->hasKeywords(['测试', ['关键字', 'keyword']])
                        ->then(function (Dialog $dialog) {
                            $dialog->send()->info('命中测试关键字');
                            return $dialog->redirect()->reactivate();
                        })

                        // depend
                        ->todo(function(Dialog $dialog, array $dependencies){

                            $typer = $dialog
                                ->send()
                                ->info('dependencies are :');

                            foreach ($dependencies as $key => $type) {
                                $typer->info("$key : $type");
                            }

                            return $dialog->redirect()->reactivate();
                        })
                        ->pregMatch('/depend/')

                        ->end();
                }
            )
            ->end();
    }

    /**
     * 情绪功能的测试. 目前测试 intent => emotion => confirmation
     * @param Stage $stage
     * @return StageDef
     */
    public function __on_test_confirmation(Stage $stage) : StageDef
    {

        return $stage
            ->onActivate(function(Dialog $dialog) {

                return $dialog
                    ->await()
                    ->askConfirm(
                        'try to confirm this. test positive emotion. '
                    );

            })
            ->onEvent(
                Dialog::HEED,
                function(Dialog $dialog) {

                    return $dialog
                        ->hearing()
                        ->isPositive()
                        ->then(function(Dialog $dialog){
                            $dialog->send()->info('is positive emotion');
                            return $dialog->redirect()->next('menu');
                        })
                        ->isNegative()
                        ->then(function(Dialog $dialog){
                            $dialog->send()->info('is negative emotion');
                            return $dialog->redirect()->next('menu');
                        })
                        ->end(function(Dialog $dialog){
                            $dialog->send()->notice('nether yes nor no');
                            return $dialog->redirect()->next('menu');
                        });
                }
            )
            ->end();
    }

    public function __on_test_memory(Stage $stage): StageDef
    {
        return $stage
            ->onActivate(function(Dialog $dialog) : Dialog {

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
            ->onEvent(
                Dialog::HEED,
                function(Dialog $dialog) : Dialog {

                    return $dialog
                        ->hearing()

                        ->is(0)
                        ->then( $dialog->redirect()->reactivate())

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

                            return $dialog->redirect()->reactivate();

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
                                    . ' value is test:%test%, test1:%test1%'
                                );

                            return $dialog->redirect()->reactivate();
                        })
                        ->end();
                }
            )
            ->end();
    }

    /**
     * 通过 stage 管道.
     * @param Stage $stage
     * @return StageDef
     */
    public function __on_stage_pipe_test(Stage $stage): StageDef
    {
        return $stage
            ->onActivate(function(Dialog $dialog) {
                $dialog
                    ->send()
                    ->info('test stage start');

                return $dialog
                    ->await()
                    ->askAny('输入一个值 (会展示这个值然后跳到下一步)');

            })
            ->onEvent(
                Dialog::HEED,
                function(Dialog $dialog, HostMsg $message) {
                    $dialog
                        ->send()
                        ->info(
                            '您输入的是:'
                            . $message->getText()
                        );
                    return $dialog->redirect()->next();
                }
            )
            ->end();
    }

}