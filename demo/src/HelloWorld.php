<?php


namespace Commune\Demo;

use Commune\Chatbot\Config\Children\DefaultMessagesConfig;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\OOContext;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\Blueprint\Message\Message;


/*---------------- 新手教程第一课, hello world --------------*/

// 如果您使用了 IDE, 可以用快捷键打开注释.
// 在 mac 版的 phpstorm 中快捷键是 cmd + /

/**
 * 创建 hello world 文件
 */
class HelloWorld extends OOContext
{
    /**
     * 定义依赖
     * @param Depending $depending
     */
    public static function __depend(Depending $depending): void
    {
    }

    /**
     * 定义退出事件捕获
     * @param Exiting $listener
     */
    public function __exiting(Exiting $listener): void
    {
    }

    /**
     * 定义上下文启动
     * @param Stage $stage
     * @return Navigator
     */
    public function __onStart(Stage $stage): Navigator
    {
        // 任何时候都执行的逻辑
        $stage->always(function(Dialog $dialog){
            $dialog->say()->info('hello world!');
        });

        // 等待用户下一次输入
        return $stage->dialog->wait();
    }

}


/*---------------- 新手教程第二课, 定义单轮对话 --------------*/

///**
// * 创建 hello world 文件
// */
//class HelloWorld extends OOContext
//{
//    /**
//     * 定义依赖
//     * @param Depending $depending
//     */
//    public static function __depend(Depending $depending): void
//    {
//    }
//
//    /**
//     * 定义退出事件捕获
//     * @param Exiting $listener
//     */
//    public function __exiting(Exiting $listener): void
//    {
//    }
//
//    /*--------- 用 talk 来定义一个单轮对话 ----------*/

//  第一节: 用 talk 定义单轮对话
//
//    /**
//     * 用 talk 定义 一个单轮对话
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onStart(Stage $stage): Navigator
//    {
//
//        return $stage->talk(
//            // 主动表表达
//            function(Dialog $dialog) : Navigator {
//                // 主动说话
//                $dialog->say()->info("请问您有什么需求?");
//
//                // 等待用户回复.
//                return $dialog->wait();
//            },
//            // 被动回复
//            function(Message $message, Dialog $dialog) : Navigator {
//
//                $dialog->say()->warning(
//
//                    // 回复消息的模板, 用 %text% 表示变量.
//                    // 底层使用了 Symfony Translator
//                    '您的回复是 %text% ',
//
//                    // 定义回复模板所用的变量
//                    [
//                        'text' => $message->getText()
//                    ]
//                );
//
//                // 重复当前 stage
//                return $dialog->repeat();
//            }
//        );
//    }
//
//}


///**
// * 创建 hello world 文件
// */
//class HelloWorld extends OOContext
//{
//    /**
//     * 定义依赖
//     * @param Depending $depending
//     */
//    public static function __depend(Depending $depending): void
//    {
//    }
//
//    /**
//     * 定义退出事件捕获
//     * @param Exiting $listener
//     */
//    public function __exiting(Exiting $listener): void
//    {
//    }
//
//
//    /**
//     * 用 talk 定义 一个单轮对话, 并且用 callable 来拆分.
//     * @param Stage $stage
//     * @return Navigator
//     */
//    public function __onStart(Stage $stage): Navigator
//    {
//
//        return $stage->talk(
//            [$this, 'talkToUser'],
//            [$this, 'hearFromUser']
//        );
//    }
//
//    public function talkToUser(Dialog $dialog) : Navigator
//    {
//        // 主动说话
//        $dialog->say()->info("请问您有什么需求?");
//
//        // 等待用户回复.
//        return $dialog->wait();
//    }
//
//    /**
//     * Message 与 Dialog 都通过依赖注入获取
//     *
//     * @param Message $message
//     * @param Dialog $dialog
//     * @return Navigator
//     */
//    public function hearFromUser(Message $message, Dialog $dialog) : Navigator
//    {
//        $dialog->say()->warning(
//
//            // 回复消息的模板, 用 %text% 表示变量.
//            // 底层使用了 Symfony Translator
//            '您的回复是 %text% ',
//
//            // 定义回复模板所用的变量
//            [
//                'text' => $message->getText()
//            ]
//        );
//
//        // 重复当前 stage
//        return $dialog->repeat();
//    }
//
//
////    /**
////     * Message 与 Dialog 都通过依赖注入获取
////     *
////     * @param Message $message
////     * @param Dialog $dialog
////     * @return Navigator
////     */
////    public function hearFromUser(Message $message, Dialog $dialog) : Navigator
////    {
////        return $dialog
////            // dialog 听到了输入的消息
////            ->hear($message)
////
////            // 精准匹配 hello
////            ->is('hello', function(Dialog $dialog) {
////                $dialog->say()->info('hello world!');
////                // 重复对话
////                return $dialog->repeat();
////            })
////
////            // php 正则匹配
////            ->pregMatch('/test/', [], function(Dialog $dialog) {
////                $dialog->say()->info("命中了 /test/ 正则");
////                return $dialog->repeat();
////            })
////
////            // php 关键字匹配
////            // 使用 二维数组作为关键字, 第一维是与的关系, 第二维是或的关系.
////            ->hasKeywords(['你', '是', ['谁', '什么']], function(Dialog $dialog){
////                $dialog->say()->info('我是 hello world 机器人');
////                return $dialog->repeat();
////            })
////
//////            // 查看上下文中可用的依赖注入对象
//////            // 不包括 IoC 容器中定义的对象.
//////            ->is('look di', function (Dialog $dialog, array $dependencies) {
//////                $dialog->say()
//////                    ->info('当前的依赖有: ')
//////                    ->info(json_encode($dependencies, JSON_PRETTY_PRINT));
//////
//////                return $dialog->repeat();
//////            })
////
////            /**
////             * 拒答的逻辑, 当上述流程没有任何返回时, 会执行 miss match 事件.
////             * 用户会收到一个默认回复. 这个回复在 $config->defaultMessages 里有定义
////             * @see DefaultMessagesConfig
////             */
////            ->end();
////    }
//
//}
