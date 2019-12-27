<?php


namespace Commune\Demo;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;


/*------------ 第四节, 学习定义 n 阶多轮对话 ------------*/

/*------- 第一节, 先掌握用字符串定义语境名称. -------*/

class UserInfo extends OOContext
{

    // 注册这个常量, 可以使语境获得简介. $context->getDef()->getDesc();
    const DESCRIPTION = 'N 阶多轮对话教学用例';


    /**
     * 定义语境的名称, 不是用 命名空间 转义, 而是直接定义字符串.
     * @return string
     */
    public static function getContextName(): string
    {
        // 注意, 仅仅支持 小写字母, 数字, '.' 和 '_' 几种符号.
        // 正则是 /[a-z0-9\-\.]+/
        return 'demo.lesions.user-info';
    }

    public function __onStart(Stage $stage): Navigator
    {
        return $stage->buildTalk()
            ->info('待会再写内容')
            ->fulfill();
    }

}

/*-------- 第二节 用 __depend 快速定义多轮对话 --------*/


///**
// * @property string $name 请问我该怎么称呼您?
// * @property string $email 请问您的邮箱是?
// */
//class UserInfo extends OOContext
//{
//
//    // 注册这个常量, 可以使语境获得简介. $context->getDef()->getDesc();
//    const DESCRIPTION = 'N 阶多轮对话教学用例';
//
//    /**
//     * 定义语境的名称, 不是用 命名空间 转义, 而是直接定义字符串.
//     * @return string
//     */
//    public static function getContextName(): string
//    {
//        // 注意, 仅仅支持 小写字母, 数字, '.' 和 '_' 几种符号.
//        // 正则是 /[a-z0-9\-\.]+/
//        return 'demo.lesions.user-info';
//    }
//
//    /**
//     * 注册依赖的实体. 只有这些实体有和合法值了, 才能进入 start stage
//     * @param Depending $depending
//     */
//    public static function __depend(Depending $depending): void
//    {
//        $depending
//            ->on('name', '请问我该怎么称呼您?')
//            ->on('email', '请问您的邮箱是?');
////         // 使用注解中的 @property 来定义 entity
////         $depending->onAnnotations();
//    }
//
//    public function __onStart(Stage $stage): Navigator
//    {
//        return $stage->buildTalk([
//            // Entity "name" 可以通过 $this->name 的方式获取
//            'name' => $this->name,
//            'email' => $this->email
//        ])
//            ->info('您的名字是 %name%, 邮箱是 %email%. ')
//            ->fulfill();
//    }
//
//
////    /**
////     * 主动定义一个 stage 来覆盖掉 entity 默认的单轮对话
////     * @param Stage $stage
////     * @return Navigator
////     */
////    public function __onEmail(Stage $stage) : Navigator
////    {
////        return $stage->buildTalk()
////            // 没有suggestions, 允许任何输入
////            ->askVerbal('请问您的邮箱是?')
////            ->hearing()
////
////            ->todo(function(Dialog $dialog, Message $message){
////                // 进行赋值, 直接赋值给 entity
////                $this->email = $message->getTrimmedText();
////
////                // 进行到下一步
////                return $dialog->next();
////            })
////                ->pregMatch('/[\w\.]+@[\w\.]+/')
////
////            ->end(function(Dialog $dialog){
////                $dialog->say()->warning('邮箱格式似乎不正确');
////                // 重新询问
////                return $dialog->repeat();
////            });
////
////    }
//
//
////    /**
////     * Hearing 方法为当前 Context 里所有的 hearing api 添加公共的匹配逻辑
////     * @param Hearing $hearing
////     */
////    public function __hearing(Hearing $hearing) : void
////    {
////        $hearing
////
////            // 模拟用户取消
////            ->todo(function(Dialog $dialog) {
////                return $dialog->cancel();
////            })
////                // 精确匹配字符串 'cancel'
////                ->is('cancel')
////
////            // 模拟退出整个会话
////            ->todo(function(Dialog $dialog) {
////                return $dialog->quit();
////            })
////                // 精确匹配字符串 'quit'
////                ->is('quit')
////
////            // hearing 内部的 to do api 必须以 otherwise 结尾.
////            ->otherwise();
////
////    }
//
//
//}