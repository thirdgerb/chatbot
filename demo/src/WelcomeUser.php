<?php


namespace Commune\Demo;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Callables\Actions\Talker;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;


/*----------- 第五课, n阶多轮对话 -----------*/

/*----------- 第一节, 用 depend entity 来定义多轮对话 -----------*/

/**
 * 欢迎用户的测试用例
 *
 * @property-read UserInfo $user  增加一个注解, 方便IDE识别
 */
class WelcomeUser extends OOContext
{
    public static function __depend(Depending $depending): void
    {
        // 标记 user entity 依赖另一个 Context
        $depending->onContext('user', 'demo.lesions.user-info');
    }

    public function __exiting(Exiting $listener): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            // 打招呼
            ->info(
                '欢迎您, %name%',
                [
                    // 直接链式调用 UserInfo 的参数
                    'name' => $this->user->name
                ]
            )
            // 打完招呼直接结束.
            ->fulfill();
    }

}


/*----------- 用 stage::dependOn 来定义依赖关系 -----------*/

///**
// * 欢迎用户的测试用例
// * @property UserInfo|null $user  增加一个注解, 方便IDE识别
// */
//class WelcomeUser extends OOContext
//{
//    public static function __depend(Depending $depending): void
//    {
//    }
//
////    /**
////     * 退出事件拦截
////     * @param Exiting $listener
////     */
////    public function __exiting(Exiting $listener): void
////    {
////        $listener
////            // 拦截依赖流程的 cancel 事件
////            ->onCancel(function(Dialog $dialog, Context $context) : ? Navigator {
////
////                $dialog->say()->info('(侦测到 cancel 指令)');
////
////                // 如果 name 有值, 就拦截事件.
////                if (
////                    $context instanceof UserInfo
////                    && $context->hasAttribute('name')
////                ) {
////                    $dialog->say()->info('(userInfo::name 参数存在, 直接进入下一步"final")');
////                    $this->user = $context;
////                    // 重定向到 falwell
////                    return $dialog->goStage('final');
////                }
////
////                // 如果 name 没值, 不做任何额外操作.
////                return null;
////            })
////            // 拦截全局的 quit 事件.
////            ->onQuit(function(Dialog $dialog){
////                $dialog->say()->info('(侦测到 quit 指令, 不做拦截)');
////                return $dialog->quit(true);
////            });
////    }
//
//    public function __onStart(Stage $stage): Navigator
//    {
//        return $stage->buildTalk()
//            ->info('这里是欢迎用户测试用例')
//            // 然后进入询问用户信息的环节
//            ->goStage('askUserInfo');
//    }
//
//    /**
//     * 询问用户信息
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onAskUserInfo(Stage $stage) : Navigator
//    {
//        return $stage
//
//            // dependOn 的回调事件是 onIntended, 这里也测试一下
//            ->onIntended(Talker::say()->info('(拿到了dependOn的回调)'))
//
//            // 定义 dependOn
//            ->dependOn(
//            'demo.lesions.user-info',
//
//            // 等价于 onIntended 回调事件.
//            function(Dialog $dialog, UserInfo $userInfo) : Navigator {
//
//                // 将拿到的结果进行赋值.
//                $this->user = $userInfo;
//
//                // 重定向到 final
//                return $dialog->goStage('final');
//            }
//        );
//    }
//
//    public function __onFinal(Stage $stage) : Navigator
//    {
//        return $stage->buildTalk()
//            ->info(
//                '您好! %name%',
//                [ 'name' => $this->user->name ]
//            )
//            ->fulfill();
//    }
//}


/*------------- 第六课 : 不相依赖的多轮对话 ---------------*/


///**
// * 欢迎用户的测试用例
// * @property UserInfo|null $user  增加一个注解, 方便IDE识别
// */
//class WelcomeUser extends OOContext
//{
//    public static function __depend(Depending $depending): void
//    {
//    }
//
//    /**
//     * 退出事件拦截
//     * @param Exiting $listener
//     */
//    public function __exiting(Exiting $listener): void
//    {
//        $listener
//            // 拦截依赖流程的 cancel 事件
//            ->onCancel(function(Dialog $dialog, Context $context) : ? Navigator {
//
//                $dialog->say()->info('(侦测到 cancel 指令)');
//
//                // 如果 name 有值, 就拦截事件.
//                if (
//                    $context instanceof UserInfo
//                    && $context->hasAttribute('name')
//                ) {
//                    $dialog->say()->info('(userInfo::name 参数存在, 直接进入下一步"final")');
//                    $this->user = $context;
//                    // 重定向到 falwell
//                    return $dialog->goStage('final');
//                }
//
//                // 如果 name 没值, 不做任何额外操作.
//                return null;
//            })
//            // 拦截全局的 quit 事件.
//            ->onQuit(function(Dialog $dialog){
//                $dialog->say()->info('(侦测到 quit 指令, 不做拦截)');
//                return $dialog->quit(true);
//            });
//    }
//
//    public function __onStart(Stage $stage): Navigator
//    {
//        return $stage->buildTalk()
//            ->info('这里是欢迎用户测试用例')
//            // 然后进入询问用户信息的环节
//            ->goStage('askUserInfo');
//    }
//
//    /**
//     * 询问用户信息
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onAskUserInfo(Stage $stage) : Navigator
//    {
//        return $stage
//
//            // dependOn 的回调事件是 onIntended, 这里也测试一下
//            ->onIntended(Talker::say()->info('(拿到了dependOn的回调)'))
//
//            // 定义 dependOn
//            ->dependOn(
//                'demo.lesions.user-info',
//
//                // 等价于 onIntended 回调事件.
//                function(Dialog $dialog, UserInfo $userInfo) : Navigator {
//
//                    // 将拿到的结果进行赋值.
//                    $this->user = $userInfo;
//
//                    // 重定向到 final
//                    return $dialog->goStage('final');
//                }
//            );
//    }
//
//    public function __onFinal(Stage $stage) : Navigator
//    {
//        return $stage->buildTalk()
//            ->askChoose(
//                '请选择您想要的功能:',
//                [
//                    1 => '进入菜单',
//                    // 字符串也可以用来做选项
//                    'q' => '退出',
//                ]
//            )
//            ->hearing()
//            ->isChoice(1, Redirector::goStage('menu'))
//            ->isChoice('q', Redirector::goFulfill())
//            ->end();
//
//    }
//
//    /**
//     * 示范通过 sleep, 进入菜单语境.
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onMenu(Stage $stage) : Navigator
//    {
//        return $stage
//
//            // 示范 fallback 事件被触发.
//            ->onFallback(Talker::say()->info('(触发了 fallback 事件)'))
//
//            ->sleepTo(
//            // 和 dependOn 一样, 直接用类名, 或者 contextName, 就可以指定目标语境
//            // 不过我们这次为了示范 Context::__construct 的用法, 允许传入一个语境实例
//                new UserMenu($this->user),
//
//                // 回调的时候, 返回 final stage
//                Redirector::goStage('final')
//            );
//    }
//
//}