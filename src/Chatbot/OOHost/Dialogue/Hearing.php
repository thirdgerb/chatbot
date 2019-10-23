<?php


namespace Commune\Chatbot\OOHost\Dialogue;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Callables\Action;
use Commune\Chatbot\OOHost\Context\Callables\HearingComponent;
use Commune\Chatbot\OOHost\Dialogue\Hearing\HearingHandler;
use Commune\Chatbot\OOHost\Dialogue\Hearing\Matcher;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Context\Context;
use Commune\Chatbot\OOHost\Dialogue\Hearing\ToDoWhileHearing;

/**
 * @property Context $self
 * @property Dialog $dialog
 * @property Message $message
 * @property bool $isMatched
 * @property Navigator|null $navigator
 *
 * @see \Commune\Chatbot\OOHost\Dialogue\Hearing\HearingHandler
 */
interface Hearing extends Matcher
{

    /*---------- 注册调用, 在生命周期中运行. ----------*/

    /**
     * 注册一个fallback. 在 end 的时候会执行.
     *
     * register a fallback caller executing at ending
     *
     * @param callable $fallback
     * @param bool $addToEndNotHead
     * @return Hearing
     */
    public function fallback(callable $fallback, bool $addToEndNotHead = true) : Hearing;

    /**
     * 修改默认的fallback.
     *
     * register the default fallback caller. run after common fallback
     *
     * @param callable $defaultFallback
     * @return Hearing
     */
    public function defaultFallback(callable $defaultFallback) : Hearing;

    /*---------- 组件化方法 ----------*/

    /**
     * 调用一个session pipe 作为中间件. 立刻执行.
     *
     * run session pipe as middleware
     *
     * @param string $sessionPipeName
     * @return Hearing
     */
    public function middleware(string $sessionPipeName) : Hearing;

    /**
     * 将 hearing 传递给 component, 从而实现组件化的定义
     *
     * pass hearing to caller, which add functions the hearing api as reusable component
     *
     * @param HearingComponent|callable $hearingComponent
     * @return Hearing
     */
    public function component(callable $hearingComponent) : Hearing;

    /**
     * 拦截器.可以做任何事.
     * 比如校验用户的身份.
     *
     * run interceptor, for example, verify user identity
     *
     * @param callable|Action $action
     * @return Hearing
     */
    public function interceptor(callable $action) : Hearing;

    /**
     * 提前执行组件. 通常是最后执行.
     * @return Hearing
     */
    public function runComponent() : Hearing;

    /**
     * 提前运行已经注册的fallback
     * 只会运行一次.
     *
     * run registered fallback actions before end()
     * only run once
     *
     * @return Hearing
     */
    public function runFallback() : Hearing;

    /**
     * 提前运行默认的 fallback
     * 只会运行一次.
     *
     * run registered default fallback action before end()
     * only run once
     *
     * @return Hearing
     */
    public function runDefaultFallback() : Hearing;

    /**
     * 无论是否已经生成了Navigator 都会执行.
     * 但有可能引起歧义.
     *
     * run action no matter navigator has been set.
     *
     * @param callable $callable
     * @return Hearing
     */
    public function always(callable $callable) : Hearing;


    /**
     * 作为链式调用的结尾.
     *
     * 会忽视掉 event 类型信息.
     * 上面的流程都处理完了还没有返回结果的时候, 会尝试调用系统默认的方法.
     *
     * 包括以下流程:
     * 0. 如果是事件, 则什么也不发生.
     * 1. 检查是否存在 __help 方法, 存在调用 Hearing::onHelp
     * 2. 检查 $fallback[] 是否存在, 存在依次调用, 如果得到 navigator 就返回
     * 3. 如果系统定义了 defaultFallback, 执行defaultFallback
     * 4. 否则返回 missMatch
     *
     * @see HearingHandler
     *
     * end the hearing api and return navigator.
     * ignore event message.
     *
     * return navigator ?? runFallback() ?? runDefaultFallback() ?? missMatch().
     *
     *
     * @param callable|null $defaultFallback
     * @return Navigator
     */
    public function end(callable $defaultFallback = null) : Navigator;


    /**
     * 不执行 end 的各种fallback
     * 命中就直接返回navigator
     * 否则直接返回 missMatch
     */
    public function heardOrMiss() : Navigator;


    /*---------- to do api ----------*/

    /**
     * to do api .
     *
     * 先定义要做什么, 再定义做的条件.
     * 这样在有些场景下更加清晰.
     *
     *  ->todoWhile()
     *      ->condition
     *      ->condition
     *  ->otherwise()
     *
     *  for you can quickly understand what dialog could do
     *
     *
     * @param callable $todo
     * @return ToDoWhileHearing
     */
    public function todo(callable $todo) : ToDoWhileHearing;

    /*---------- 匹配消息 ----------*/

    /**
     * hear 命中, 但没有处理后产生navigator的情况.
     * 可以在多个条件判断后加一个heard, 作为共同的处理逻辑.
     * 不过 heard 在整个判断流程中只会执行一次.
     *
     * @param Action|callable $action
     * @return Hearing
     */
    public function then(callable $action) : Hearing;


    /*---------- 直接运行意图. ----------*/

    /**
     * 由 NLU 传递来的任何intent 如果存在, 则直接执行 intent::navigate
     * @return static
     */
    public function runAnyIntent() : Hearing;


    /**
     * 主动匹配一个 intent.
     * 即便 NLU 没有传递, 也会去尝试匹配.
     * 匹配到了就直接执行默认方法
     *
     * @param string $intentName
     * @return static
     */
    public function runIntent(string $intentName) : Hearing;


    /**
     * 尝试从一批intents 中匹配一个intent.
     * 无论 NLU 是否有传递, 会主动进行匹配.
     * intentName 可以传递前缀.
     * 执行 intent 自己的action
     *
     * @param array $intentNames
     * @return static
     */
    public function runIntentIn(array $intentNames) : Hearing;


    /*---------- debug 模式 ----------*/

    public function debugMatch() : Hearing;
}