<?php


namespace Commune\Demo;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\Demo\Cases\Maze\MazeInt;
use Commune\Chatbot\App\Callables\StageComponents\Menu;


/*------------ 第六节 不相依赖的 N 阶多轮对话 ------------*/

/**
 * 用户的菜单
 *
 * @property-read UserInfo $user 仍然依赖用户信息.
 */
class UserMenu extends OOContext
{

    public function __construct(UserInfo $user)
    {
        // 原始 __construct 方法接受一个 map
        // map 里的值都会付给当前 Context, 然后可以用 $context->{$key} 的方式来调用
        // 注意 map 里的值应该全部都可以序列化
        // 最好是基础值(is_scalar), Message 对象, Context 对象
        // 它们会序列化后保存在 session 的上下文里.
        parent::__construct(get_defined_vars());
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk([
                'name' => $this->user->name
            ])
            ->askChoose(
                '您好, %name%, 请选择您需要的操作:',
                [
                    1 => '迷宫小游戏',
                    2 => '返回'
                ]
            )
            ->hearing()

            // 进入迷宫游戏
            ->todo(Redirector::goStage('maze'))
                // 如果用户答案命中了 1选项
                ->isChoice(1)

            // 返回
            ->todo(Redirector::goCancel())
                ->isChoice(2)
                // 如果是 "cancel" 字符串, 也可以直接执行返回.
                ->is('cancel')

            ->otherwise()

            // 拒答
            ->end(function(Dialog $dialog){
                $dialog->say()->warning('对不起, 您的选项不存在');
                // 重复语境
                return $dialog->repeat();
            });
    }

    public function __onMaze(Stage $stage) : Navigator
    {
        // 通过 sleep to, 进入到迷宫游戏
        return $stage->sleepTo(
            MazeInt::getContextName(),
            // 迷宫游戏退出后的回调方法
            function(Dialog $dialog){
                $dialog->say()->info('(迷宫游戏结束)');

                // 回到 start 环节.
                return $dialog->goStage('start');
            }
        );
    }

}

/*------------ 使用 Menu 组件重构的对话 ------------*/

///**
// * 用户的菜单
// *
// * @property-read UserInfo $user 仍然依赖用户信息.
// */
//class UserMenu extends OOContext
//{
//
//    public function __construct(UserInfo $user)
//    {
//        // 原始 __construct 方法接受一个 map
//        // map 里的值都会付给当前 Context, 然后可以用 $context->{$key} 的方式来调用
//        // 注意 map 里的值应该全部都可以序列化
//        // 最好是基础值(is_scalar), Message 对象, Context 对象
//        // 它们会序列化后保存在 session 的上下文里.
//        parent::__construct(get_defined_vars());
//    }
//
//    public static function __depend(Depending $depending): void
//    {
//    }
//
//    public function __exiting(Exiting $listener): void
//    {
//    }
//
//    public function __onStart(Stage $stage): Navigator
//    {
//        // 定义了 Menu 组件.
//        $menu = (new Menu(
//            '您好, %name%, 请选择您需要的操作:',
//            [
//                // 直接用语境作为值, 会自动调用该语境的 desc
//                MazeInt::class,
//
//                // 用 stage 名称作为值
//                '迷宫' => 'maze',
//
//                // 用闭包作为值
//                '返回' => Redirector::goCancel()
//            ]
//
//        ))
//            ->withSlots(['name' => $this->user->name])
//            ->onHearing(function(Hearing $hearing) {
//                $hearing->defaultFallback(function(Dialog $dialog){
//                    $dialog->say()->warning('对不起, 您的选项不存在');
//                    // 重复语境
//                    return $dialog->repeat();
//                });
//            });
//
//        // 用组件去 build stage
//        return $stage->component($menu);
//    }
//
//    public function __onMaze(Stage $stage) : Navigator
//    {
//        // 通过 sleep to, 进入到迷宫游戏
//        return $stage->sleepTo(
//            MazeInt::getContextName(),
//            // 迷宫游戏退出后的回调方法
//            function(Dialog $dialog){
//                $dialog->say()->info('(迷宫游戏结束)');
//
//                // 回到 start 环节.
//                return $dialog->goStage('start');
//            }
//        );
//    }
//}