<?php


namespace Commune\Chatbot\OOHost\Context;


use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Callables\Action;
use Commune\Chatbot\OOHost\Context\Callables\HearingComponent;
use Commune\Chatbot\OOHost\Context\Callables\Prediction;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Chatbot\OOHost\Emotion\Feeling;

/**
 * @property Context $self
 * @property Dialog $dialog
 * @property Message $message
 * @property bool $heard
 * @property Navigator|null $navigator
 *
 * @see \Commune\Chatbot\OOHost\Context\Listeners\HearingHandler
 */
interface Hearing
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
     * 上面的流程都处理完了还没有返回结果的时候, 会尝试调用 $fallback
     * 否则返回 missMatch
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
     * @return ToDoWhileHearingMessage
     */
    public function todo(callable $todo) : ToDoWhileHearingMessage;

    /*---------- 匹配消息 ----------*/

    /**
     * 自定义的监听.
     * 用一个 prediction callable 判断是否命中条件.
     * 命中后执行 interceptor
     *
     * run action only if expecting prediction return true
     *
     * @param Prediction|callable $prediction  return bool
     * @param Action|callable $action
     * @return Hearing
     */
    public function expect(
        callable $prediction,
        callable $action = null
    ) : Hearing;


    /**
     * message 是一个字符串.
     *
     * if message->getText() exactly match the $text
     *
     * @param string $text
     * @param callable|null $action
     * @return Hearing
     */
    public function is(
        string $text,
        callable $action = null
    ) : Hearing;

    /**
     * Message->isEmpty() === true
     *
     * @param callable|null $action
     * @return Hearing
     */
    public function isEmpty(
        callable $action = null
    ) : Hearing;

    /**
     * 通过正则匹配获取数据.
     * keys 命中的参数会作为变量传递给 interceptor
     * 最好不要用这一步.
     *
     * use regex to define condition.
     * the variable extract by regex pattern,
     * will assign to arrayMessage variables, named by $keys
     *
     * @param string $pattern
     * @param string[] $keys
     * @param Action|callable $action   message is ArrayMessage
     * @return Hearing
     */
    public function pregMatch(
        string $pattern,
        array $keys = [],
        callable $action = null
    ): Hearing;


    /**
     * 判断消息是否符合某种情感.
     *
     * to feel message match certain feeling
     * parser @see Feeling
     *
     * @param string $emotionName
     * @param Action|callable $action
     * @return Hearing
     */
    public function feels(
        string $emotionName,
        callable $action = null
    ) : Hearing;

    /**
     * @param callable $action
     * @return Hearing
     */
    public function isPositive(callable $action = null) : Hearing;

    /**
     * @param callable|null $action
     * @return Hearing
     */
    public function isNegative(callable $action = null) : Hearing;

    /**
     * 由 NLU 传递来的intent 如果存在
     * 则执行不为null 的intentAction
     * 否则 执行 intent 自带的 action
     *
     * @param callable|null $intentAction    $message is IntentMessage
     * @return Hearing
     */
    public function isAnyIntent(
        callable $intentAction = null
    ) : Hearing;

    /**
     * 主动匹配一个 intent.
     * 即便 NLU 没有传递, 也会去尝试匹配.
     *
     * @param string $intentName
     * @param callable|null $intentAction   $message is IntentMessage
     * @return Hearing
     */
    public function isIntent(
        string $intentName,
        callable $intentAction = null
    ) : Hearing;

    /**
     * 尝试从一批intents 中匹配一个intent.
     * 无论 NLU 是否有传递, 会主动进行匹配.
     *
     * intentName 可以传递前缀.
     *
     * 命中后, 优先执行不为null 的intentAction
     * 否则执行 intent 自己的action
     *
     * @param array $intentNames
     * @param callable|null $intentAction  $message is IntentMessage
     * @return Hearing
     */
    public function isIntentIn(
        array $intentNames,
        callable $intentAction = null
    ) : Hearing;

    /**
     * 是否匹配到了entity 类型
     *
     * @param string $entityName
     * @param callable|null $interceptor
     * @return Hearing
     */
    public function hasEntity(
        string $entityName,
        callable $interceptor = null
    ) : Hearing;


    /**
     * 存在entity, 并且值 equals(==) $expect
     *
     * @param string $entityName
     * @param mixed $expect
     * @param callable|null $interceptor
     * @return Hearing
     */
    public function hasEntityValue(
        string $entityName,
        $expect,
        callable $interceptor = null
    ) : Hearing;

    /**
     * 判断message 的 $message->getMessageType 是否符合.
     *
     * @param string $messageType
     * @param Action|callable $action
     * @return Hearing
     */
    public function isTypeOf(
        string $messageType,
        callable $action = null
    ) : Hearing;

    /**
     * 判断传入的 message 是否是某个 MessageSubClass 的实例.
     *
     * @param string $messageClazz
     * @param Action|callable $action
     * @return Hearing
     */
    public function isInstanceOf(
        string $messageClazz,
        callable $action = null
    ) : Hearing;


    /**
     * 只要有answer, 不管上文有没有命中过.
     *
     * @param Action|callable $action   $message could be Answer
     * @return Hearing
     */
    public function isAnswer(callable $action = null) : Hearing;


    /**
     * 之前提了一个问题, 答案命中了问题的一个建议的情况.
     * 可以与 answered 挑选使用.
     *
     * @param int|string $suggestionIndex
     * @param Action|callable $action   $message could be Choice
     * @return Hearing
     */
    public function isChoice(
        $suggestionIndex,
        callable $action = null
    ) : Hearing;

    /**
     * 有多个choice 中的一个
     * @param int[] $choices
     * @param callable|null $action
     * @return Hearing
     */
    public function hasChoice(
        array $choices,
        callable $action = null
    ) : Hearing;

    /**
     * 尝试匹配一个临时定义的命令
     * 并把匹配成功的 CommandMessage 传递给interceptor
     *
     * @param string $signature
     * @param Action|callable $action  $message is CommandMessage
     * @return Hearing
     */
    public function isCommand(
        string $signature,
        callable $action = null
    ) : Hearing;


    /**
     * 用php做比较脏的关键词检查.
     * 最好不要沦落到这一步.
     *
     * @param array $keyWords   [ 'word1', 'word2', ['synonym1', 'synonym2']]
     * @param Action|callable $action
     * @return Hearing
     */
    public function hasKeywords(
        array $keyWords,
        callable $action = null
    ) : Hearing;



    /**
     * hear 命中, 但没有处理后产生navigator的情况.
     * 可以在多个条件判断后加一个heard, 作为共同的处理逻辑.
     * 不过 heard 在整个判断流程中只会执行一次.
     *
     * @param Action|callable $action
     * @return Hearing
     */
    public function heard(callable $action) : Hearing;


    /**
     * 如果不主动拦截, 则event 消息都会被忽视.
     * @param string $eventName
     * @param callable|Action|null $action    $message is EventMessage
     * @return Hearing
     */
    public function isEvent(string $eventName, callable $action = null) : Hearing;

    /**
     * @param string[] $eventName
     * @param callable|null $action
     * @return Hearing
     */
    public function isEventIn(array $eventName, callable $action = null) : Hearing;



}