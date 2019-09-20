<?php


namespace Commune\Chatbot\OOHost\Context;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Callables\Interceptor;
use Commune\Chatbot\OOHost\Context\Callables\StageComponent;
use Commune\Chatbot\OOHost\Context\Stages\CallbackStageRoute;
use Commune\Chatbot\OOHost\Context\Stages\OnStartStage;
use Commune\Chatbot\OOHost\Context\Stages\StartStageRoute;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Hearing;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
use Commune\Chatbot\OOHost\Directing\Navigator;

/**
 * @property-read string $name  stage name
 * @property-read Context $self
 * @property-read Dialog $dialog
 * @property-read null|Message $value
 * @property-read Navigator|null $navigator
 *
 * @see StartStageRoute
 * @see CallbackStageRoute
 */
interface Stage
{
    /**
     * 进入一个stage, 启动
     * @return bool
     */
    public function isStart() : bool;

    /**
     * stage 拿到了一个回调 message
     * 可能是 yield dependOn 或者 wait 拿到的message
     * @return bool
     */
    public function isCallback() : bool;

    /**
     * stage 进入sleep, 被重新唤醒, 没有message
     * @return bool
     */
    public function isFallback() : bool;

    /*------ 组件化 ------*/

    /**
     * 用组件的方式定义一个 checkpoint
     * @param callable|StageComponent $stageComponent
     * @return Navigator
     */
    public function component(callable $stageComponent) : Navigator;

    /*------ 事件 ------*/

    /**
     * start 时候触发.
     *
     * @param callable $interceptor
     * @return Stage
     */
    public function onStart(callable $interceptor) : Stage;

    /**
     * callback 时触发.
     *
     * @param callable $interceptor
     * @return Stage
     */
    public function onCallback(callable $interceptor) : Stage;

    /**
     * fallback 的时候才会触发
     * @param callable $interceptor
     * @return Stage
     */
    public function onFallback(callable $interceptor) : Stage;

    /**
     * use hearing api
     * @return Hearing
     */
    public function hearing();

    /**
     * 不做任何事
     * 等待用户发一个消息.
     *
     * @param callable $hearMessage
     * @return Navigator
     */
    public function wait(
        callable $hearMessage
    ) : Navigator;


    /*------ 等待用户输入. ------*/

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
     * 用链式调用的 api 来定义 talk 的流程.
     * talk 之外的情况并不适用.
     *
     * @param array $slots 默认的slots
     * @return OnStartStage
     */
    public function buildTalk(array $slots = []) : OnStartStage;

    /*------ 依赖信息 ------*/


    /**
     * 依赖一个context.
     * 目标 context fulfill 之后, 会作为 message 参数回调.
     *
     * 目标 context 退出或遭遇异常( onCancel, onFail, onReject )
     * 也会使当前context遭遇同样的事件.
     *
     * 目标context 和当前 context 在同一个thread中.
     *
     * @param string|Context $dependency
     * @param callable|null $callback
     * @param array|null $stages  是否要指定 stages. 通常不用.
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
     * 从当前thread, 跳转到另一个context新建的thread
     * $to 为null 的时候, fallback 到最近的 thread
     *
     * 会把当前 thread 压入 $sleeping 栈的尾部
     *
     * 如果会话(dialog) 运行中的 thread 都退出(fulfill, cancel)后
     * 会重新唤醒(wake)当前的 thread
     *
     * 不填写 $wake 时, 默认回到 default stage
     *
     * @param Context|string $to
     * @param callable|null $wake
     * @return Navigator
     */
    public function sleepTo($to, callable $wake = null) : Navigator;

    /**
     * @param null $to
     * @param string $level
     * @return Navigator
     */
    public function replaceTo($to = null, string $level = Redirect::THREAD_LEVEL) : Navigator;

    /**
     * 由于调用了一个服务, 依赖该服务的回调,
     * 所以将当前 thread 从会话中移除.
     * 只有在服务回调后才会唤醒.
     *
     * 如果定义了 $to, 则会进入一个新 thread
     * 否则会fallback 到上一个thread.
     *
     * 当服务回调时, 会话当前的thread, 会sleepTo 唤醒的yielding thread
     *
     * @param string|Context|null $to
     * @param callable $wake
     * @return Navigator
     */
    public function yieldTo(
        $to = null,
        callable $wake
    ) : Navigator;

}