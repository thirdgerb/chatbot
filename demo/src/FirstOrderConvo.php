<?php


namespace Commune\Demo;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Callables\Actions\Talker;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;

/*----------- 第三课, 定义一阶多轮对话 ------------*/

/**
 * 一阶多轮对话的示例.
 */
class FirstOrderConvo extends OOContext
{
    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
    }

    /**
     * 启动环节.
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onStart(Stage $stage): Navigator
    {
        return $stage->talk(
            function(Dialog $dialog) {

                $dialog->say()->info('输入任何信息进入下一步');

                // 等待用户输入
                return $dialog->wait();
            },
//             Talker::say()->info('输入任何信息进入下一步'),
            function(Dialog $dialog) {
                return $dialog->goStage('final');
            }
//             Redirector::goStage('final')
        );
    }


    /**
     * 最终步, 结束对话.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onFinal(Stage $stage) : Navigator
    {
        $name = $stage->name;
        $stage->dialog->say()->info("到达了 $name 环节, 流程退出.");

        // 结束流程.
        return $stage->dialog->fulfill();
    }

}

/*----------- 第二节, 定义多个 stage ------------*/

///**
// * 一阶多轮对话的示例.
// */
//class FirstOrderConvo extends OOContext
//{
//    public static function __depend(Depending $depending): void
//    {
//    }
//
//    public function __exiting(Exiting $listener): void
//    {
//    }
//
//    /**
//     * 启动环节.
//     *
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onStart(Stage $stage): Navigator
//    {
//        return $stage->talk(
//            Talker::say()->info('输入任何信息进入下一步'),
//            Redirector::goStage('step1')
//        );
//    }
//
//    /**
//     *
//     * @stage   用 stage 注解来定义, 而不是用前缀
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function step1(Stage $stage) : Navigator
//    {
//        $name = $stage->name;
//        return $stage->talk(
//            Talker::say()
//                ->info("进入了 stage $name")
//                ->info('输入任何信息进入下一步'),
//            Redirector::goStage('step2')
//        );
//    }
//
//    /**
//     * 第二步
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onStep2(Stage $stage) : Navigator
//    {
//        $name = $stage->name;
//        return $stage->talk(
//            Talker::say()
//                ->info("进入了 stage $name")
//                ->info('输入任何信息进入下一步'),
//            Redirector::goStage('final')
//        );
//    }
//
//
//    /**
//     * 最终步, 结束对话.
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onFinal(Stage $stage) : Navigator
//    {
//        $name = $stage->name;
//        $stage->dialog->say()->info("到达了 $name 环节, 流程退出.");
//
//        // 结束流程.
//        return $stage->dialog->fulfill();
//    }
//
//}

/*----------- 第三节, 定义有分支的 stage pipeline ------------*/


///**
// * 一阶多轮对话的示例.
// */
//class FirstOrderConvo extends OOContext
//{
//    public static function __depend(Depending $depending): void
//    {
//    }
//
//    public function __exiting(Exiting $listener): void
//    {
//    }
//
//    /**
//     * 启动环节.
//     *
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onStart(Stage $stage): Navigator
//    {
//        return $stage->talk(
//            Talker::say()->info('输入任何信息进入下一步'),
//            Redirector::goStage('step1')
//        );
//    }
//
//    /**
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onStep1(Stage $stage) : Navigator
//    {
//        $name = $stage->name;
//
//        return $stage
//
//            // 定义了 onStart 事件, 只在start stage 时执行.
//            ->onStart(
//                // say 方法传入数组(模板slots), 可以让所有后续的 info, warning等方法共用.
//                Talker::say(['name' => $name])
//                    ->info("进入了 stage $name")
//            )
//
//            // talk
//            ->talk(
//                function(Dialog $dialog) : Navigator {
//
//                    // 向用户抛出选择题.
//                    $dialog->say()->askChoose(
//                        '请选择您想走的路线',
//                        [
//                            1 => '路线1',
//                            2 => '路线2',
//                        ]
//                    );
//
//                    return $dialog->wait();
//                },
//
//                function(Dialog $dialog)  : Navigator {
//
//                    return $dialog->hear()
//
//                        ->isChoice(1, function(Dialog $dialog){
//
//                            // 使用 goStagePipes 可以定义一个stage 管道
//                            return $dialog->goStagePipes(['step2', 'step3', 'final']);
//                        })
//
//                        ->isChoice(2, Redirector::goStageThrough(['step3', 'step2', 'final']))
//                        ->end();
//
//                }
//            );
//    }
//
//    public function __onStep2(Stage $stage) : Navigator
//    {
//        // 这次我们封装一个内部方法, 来复用代码.
//        return $this->goStep($stage);
//    }
//
//    public function __onStep3(Stage $stage) : Navigator
//    {
//        return $this->goStep($stage);
//    }
//
//    /**
//     * 进入到下一步.
//     * @param Stage $stage
//     * @return Navigator
//     */
//    protected function goStep(Stage $stage) : Navigator
//    {
//        $name = $stage->name;
//        return $stage->talk(
//            Talker::say()
//                ->info("经过了 stage : $name")
//                ->info("输入任何信息进入下一步"),
//            function (Dialog $dialog) : Navigator {
//
//                // 使用 next 方法, 如果管道中有下一步, 则进入下一步
//                // 否则执行 fulfill 方法.
//                return $dialog->next();
//            }
//        );
//    }
//
//    /**
//     * 最终步, 结束对话.
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onFinal(Stage $stage) : Navigator
//    {
//        $name = $stage->name;
//        $stage->dialog->say()->info("到达了 $name 环节, 流程退出.");
//
//        // 结束流程.
//        return $stage->dialog->fulfill();
//    }
//
//}

/*-------------- 第四节 使用 buildTalk -------------*/

///**
// * 一阶多轮对话的示例.
// */
//class FirstOrderConvo extends OOContext
//{
//    public static function __depend(Depending $depending): void
//    {
//    }
//
//    public function __exiting(Exiting $listener): void
//    {
//    }
//
//    /**
//     * 启动环节.
//     *
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onStart(Stage $stage): Navigator
//    {
//        // 创建 builder
//        return $stage->buildTalk()
//            // start 阶段
//            ->info('输入任何信息进入下一步')
//
//            // 通过 hearing, 进入 callback 阶段
//            ->hearing()
//            ->end(Redirector::goStage('step1'));
//    }
//
//    /**
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onStep1(Stage $stage) : Navigator
//    {
//        $name = $stage->name;
//
//        return $stage->buildTalk(['name' => $name])
//            ->info("进入了 stage $name")
//            ->askChoose(
//                '请选择您想走的路线',
//                [
//                    1 => '路线1',
//                    2 => '路线2',
//                ]
//            )
//            ->hearing()
//            ->isChoice(1, function(Dialog $dialog){
//
//                // 使用 goStagePipes 可以定义一个stage 管道
//                return $dialog->goStagePipes(['step2', 'step3', 'final']);
//            })
//
//            ->isChoice(2, Redirector::goStageThrough(['step3', 'step2', 'final']))
//            ->end();
//
//    }
//
//    public function __onStep2(Stage $stage) : Navigator
//    {
//        // 这次我们封装一个内部方法, 来复用代码.
//        return $this->goStep($stage);
//    }
//
//    public function __onStep3(Stage $stage) : Navigator
//    {
//        return $this->goStep($stage);
//    }
//
//    /**
//     * 进入到下一步.
//     * @param Stage $stage
//     * @return Navigator
//     */
//    protected function goStep(Stage $stage) : Navigator
//    {
//        $name = $stage->name;
//        return $stage->buildTalk()
//            ->info("经过了 stage : $name")
//            ->info("输入任何信息进入下一步")
//            ->hearing()
//            ->end(Redirector::goNext());
//    }
//
//    /**
//     * 最终步, 结束对话.
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onFinal(Stage $stage) : Navigator
//    {
//        $name = $stage->name;
//        return $stage->buildTalk()
//            ->info("到达了 $name 环节, 流程退出.")
//            ->fulfill();
//    }
//
//}
