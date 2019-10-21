<?php


namespace Commune\Chatbot\OOHost\Context;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Callables\Interceptor;
use Commune\Chatbot\OOHost\Context\Callables\StageComponent;
use Commune\Chatbot\OOHost\Context\Stages\CallbackStageRoute;
use Commune\Chatbot\OOHost\Context\Stages\OnStartStage;
use Commune\Chatbot\OOHost\Context\Stages\StartStageRoute;
use Commune\Chatbot\OOHost\Context\Stages\SubDialogBuilder;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * Stage 用来定义一个单轮对话的 Builder
 * 一个 Context 的多轮对话生命周期中, 势必要经过多个 stage.
 *
 * 最基本的单轮对话是: bot说话(可选) -> 用户说话 -> bot 回复.
 * 在分形几何式的多轮对话中, 一个 stage 期待的可能不只是用户的消息, 而是另一个多轮对话的结果.
 * 此外 A Context 进入到 c stage 时, 可能因为种种原因而暂时中断, 直到重新被重新唤醒.
 *
 *
 * 因此在 stage 的生命周期中, 有以下几种基本状态:
 *
 * - isStart : 进入一个 stage 时执行.
 *
 * - wait => callback , 等待用户消息 => 收到用户的消息.
 * - depend => intended , 等待另一个 context => 得到另一个 context 结果.
 * - sleep => wake , 挂起, 进入别的对话 => 被的对话结束, 唤醒当前会话.
 * - yield => recall , 让出控制权, 等待异步回调 => 获得回调, 抢占控制权. (尚未实装)
 *
 *
 * Stage 就用来定义以上各种状态下的响应逻辑.
 *
 *
 * 以下是部分开放的变量.
 *
 * @property-read string $name
 * stage 的名称.
 *
 * @property-read Context $self
 * 当前 context 的实例
 *
 * @property-read Dialog $dialog
 * 当前状态下的 Dialog
 *
 * @property-read null|Message|Context $value
 * Stage 拿到的入参.
 *
 * @property Navigator|null $navigator
 * Stage 执行逻辑中生成的 Navigator
 *
 *
 * Stage 的 interface 是同一个, 但各种状态拥有不同的实例. 具体可查看:
 *
 * @see StartStageRoute
 * @see CallbackStageRoute
 * @see FallbackStageRoute
 * @see IntendedStageRoute
 */
interface Stage
{
    /**
     * 进入一个 stage 时.
     * @return bool
     */
    public function isStart() : bool;

    /**
     * Wait -> Callback
     * stage 拿到了来自用户的 message
     * @return bool
     */
    public function isCallback() : bool;

    /**
     * Sleep -> Fallback
     * stage 处于 sleeping状态, 由于别的 context 结束了, 使自己被重新唤醒
     * @return bool
     */
    public function isFallback() : bool;

    /**
     * Depend -> Intended
     * stage depend 另一个 Context, 当另一个Context Fulfill的时候, 重新进入当前的Context
     * @return bool
     */
    public function isIntended() : bool;

    /*------ 组件化 ------*/

    /**
     * 用组件的方式定义一个 checkpoint
     *
     * @param callable|StageComponent $stageComponent
     * @return Navigator
     */
    public function component(callable $stageComponent) : Navigator;

    /*------ 事件API ------*/

    /**
     * isStart 时候触发.
     *
     * @param callable $interceptor
     * @return Stage
     */
    public function onStart(callable $interceptor) : Stage;

    /**
     * isCallback 时触发.
     *
     * @param callable $interceptor
     * @return Stage
     */
    public function onCallback(callable $interceptor) : Stage;

    /**
     * isFallback 的时候才会触发
     * @param callable $interceptor
     * @return Stage
     */
    public function onFallback(callable $interceptor) : Stage;


    /**
     * isIntended 状态时触发
     * @param callable $interceptor
     * @return Stage
     */
    public function onIntended(callable $interceptor) : Stage;

    /*------ 状态组合 API ------*/


    /**
     * 与用户进行对话
     *
     * @param callable|Interceptor $talkToUser 说话给用户.
     * 参数 Context $self, Dialog $dialog
     * 返回值 : ? Navigator
     *
     * @param callable|null $hearFromUser 听用户说话. 为null 则调用 next()
     * 参数 Context $self, Dialog $dialog, Message $message
     * 返回值 : ? Navigator
     *
     * @return Navigator
     */
    public function talk(
        callable $talkToUser,
        callable $hearFromUser = null
    ) : Navigator;



    /**
     * 依赖一个context.
     * 目标 context fulfill 之后, 会作为 message 参数回调给 $callback.
     *
     * 目标 context 退出或遭遇异常( onCancel, onFail, onReject )
     * 也会使当前context遭遇同样的事件, 可以在 Context::__exiting 中拦截.
     *
     * 通过 Depend 关系, 构成了 Thread
     *
     * @param string|Context $dependency
     * 依赖的目标 Context
     *
     * @param callable|null
     * $callback 回调方法. 不填的话, 默认执行 next()
     *
     * @param array|null $stages
     * 前往目标 context 时, 不执行默认的 start, 而是执行指定的 $stages. 通常不要用. k:w
     *
     *
     * @return Navigator
     */
    public function dependOn(
        $dependency,
        callable $callback = null,
        array $stages = null
    ) : Navigator;


    /**
     * 主要的跳转策略.
     *
     * 从当前thread, 跳转到另一个 context 新建的thread
     * $to 为null 的时候, fallback 到最近的 thread
     *
     * 会把当前 thread 压入 $sleeping 栈的尾部
     *
     * 如果会话(dialog) 运行中的 thread 都退出(fulfill, cancel)后
     * 会重新唤醒(wake)当前的 thread
     *
     * 不填写 $wake 时, 默认回到 default stage
     *
     * @param Context|string|null $to
     * @param callable|null $wake
     * @return Navigator
     */
    public function sleepTo($to, callable $wake = null) : Navigator;

    /*------ 会话嵌套 ------*/

    /**
     * 开启一个子会话. 与当前会话的生命周期完全隔离.
     * 共享 session 内的记忆.
     *
     * 相当于当前会话是一个中间件.
     *
     * @param string $belongsTo
     * @param callable $rootContextMaker
     * @param Message|null $message 如果不传, 就继承当前 dialog 的 currentMessage
     * @param bool $keepAlive
     * @return SubDialogBuilder
     */
    public function onSubDialog(
        string $belongsTo,
        callable $rootContextMaker,
        Message $message = null,
        bool $keepAlive = true
    ) : SubDialogBuilder;



    /*------ 由于 talk 是最常用的 stage, 所以有一些额外的 builder 提供 ------*/

    /**
     * 创建一个 hearing api. 只有在 callback 的时候生效.
     *
     * use hearing api
     * @return Hearing
     */
    public function hearing();


    /**
     * 不做任何事情.
     * 等待用户发一个消息.
     *
     * isCallback 状态时, 会用用户的 message 回调 $hearMessage 方法.
     *
     * @param callable $hearMessage
     * @return Navigator
     */
    public function wait(
        callable $hearMessage
    ) : Navigator;


    /**
     * 用链式调用的 api 来定义 talk 的流程.
     *
     * 可以快速定义 isStart 和 isCallback 状态下的方法. 对其它情况不适用.
     *
     * @param array $slots 默认的slots
     * @return OnStartStage
     */
    public function buildTalk(array $slots = []) : OnStartStage;



    // yield 机制尚未正式启用.
    //
    ///**
    // * 由于调用了一个服务, 依赖该服务的回调,
    // * 所以将当前 thread 从会话中移除.
    // * 只有在服务回调后才会唤醒.
    // *
    // * 如果定义了 $to, 则会进入一个新 thread
    // * 否则会fallback 到上一个thread.
    // *
    // * 当服务回调时, 会话当前的thread, 会sleepTo 唤醒的yielding thread
    // *
    // * @param string|Context|null $to
    // * @param callable $wake
    // * @return Navigator
    // */
    //public function yieldTo(
    //    $to = null,
    //    callable $wake
    //) : Navigator;

}