<?php


namespace Commune\Chatbot\OOHost\Dialogue\Hearing;

use Commune\Chatbot\App\Messages\ArrayMessage;
use Commune\Chatbot\Blueprint\Message\QA\Question;
use Commune\Chatbot\Blueprint\Message\VerbalMsg;
use Commune\Chatbot\OOHost\Context\Callables\Action;
use Commune\Chatbot\OOHost\Context\Callables\Prediction;
use Commune\Chatbot\OOHost\Emotion\Feeling;
use Commune\Components\Predefined\Intents\Dialogue\HelpInt;
use Commune\Support\SoundLike\SoundLikeInterface;

interface Matcher
{

    /*------- 匹配事件 -------*/

    /**
     * 如果不主动拦截, 则event 消息都会被忽视.
     * @param string $eventName
     * @param callable|Action|null $action    $message is EventMessage
     * @return static
     */
    public function isEvent(string $eventName, callable $action = null) : Matcher;

    /**
     * @param string[] $eventName
     * @param callable|null $action
     * @return static
     */
    public function isEventIn(array $eventName, callable $action = null) : Matcher;


    /*------- php matcher -------*/

    /**
     * 自定义的监听.
     * 用一个 prediction callable 判断是否命中条件.
     * 命中后执行 interceptor
     *
     * run action only if expecting prediction return true
     *
     * @param Prediction|callable $prediction  return bool
     * @param Action|callable $action
     * @return static
     */
    public function expect(
        callable $prediction,
        callable $action = null
    ) : Matcher;


    /**
     * 检查 message 的 trimmedText 是否等于目标字符串. 不区分大小写, 精确匹配
     *
     * @param string $text
     * @param callable|null $action
     * @return static
     */
    public function is(
        string $text,
        callable $action = null
    ) : Matcher;


    /**
     * Message->isEmpty() === true
     *
     * @param callable|null $action
     * @return static
     */
    public function isEmpty(
        callable $action = null
    ) : Matcher;

    /**
     * 通过正则匹配获取数据.
     * 正则按顺序获取的参数, 会分配到一个数组中, 键名依次用 $keys 的值
     * 匹配后的结果作为 ArrayMessage 传递给 $action
     * 此外, 也可以用 matchEntity() 方法调用 php 实现的EntityExtractor, 原理类似敏感词匹配.
     *
     * 最好不要用这种方法. 而是依赖 NLU 去匹配.
     *
     * use regex to define condition.
     * the variable extract by regex pattern,
     * will assign to arrayMessage variables, named by $keys
     *
     * @param string $pattern  查询的正则
     * @param string[] $keys  正则中用 () 匹配到的参数, 会依次传给这里定义的变量.
     * @param Action|callable $action   命中正则后, 传入的是一个 ArrayMessage
     * @see ArrayMessage
     *
     * @return static
     */
    public function pregMatch(
        string $pattern,
        array $keys = [],
        callable $action = null
    ): Matcher;

    /**
     * 判断输入信息是否是口头或文字的.
     *
     * 相当于 instanceOf(Verbal::class)
     *
     * @see VerbalMsg
     *
     * @param callable|null $action
     * @return Matcher
     */
    public function isVerbal(
        callable $action = null
    ) : Matcher;

    /**
     * 判断传入的 message 是否是某个 MessageSubClass 的实例.
     *
     * @param string $messageClazz
     * @param Action|callable $action
     * @return static
     */
    public function isInstanceOf(
        string $messageClazz,
        callable $action = null
    ) : Matcher;

    /**
     * 发音相似. 目前应该只支持中文.
     * 用于弥补其它系统对语音识别有限的问题.
     *
     * @param string $text
     * @param callable $action
     * @param string $lang
     * @return static
     */
    public function soundLike(
        string $text,
        callable $action = null,
        string $lang = SoundLikeInterface::ZH
    ) : Matcher ;

    /**
     * @param string $text
     * @param int $type
     * @param callable|null $action
     * @param string $lang
     * @return static
     */
    public function soundLikePart(
        string $text,
        int $type = SoundLikeInterface::COMPARE_ANY_PART,
        callable $action = null,
        string $lang = SoundLikeInterface::ZH
    ) : Matcher ;


    /**
     * 如果nlu没匹配上, 就用系统自带的 entity extractor 去匹配.
     * 通常就是关键词匹配算法.
     *
     * @param string $entityName
     * @param callable|null $action
     * @return static
     */
    public function matchEntity(
        string $entityName,
        callable $action = null
    ) : Matcher;


    /*------- question matcher -------*/

    /**
     * 主动匹配一个question
     * @param Question $question
     * @return static
     */
    public function matchQuestion(Question $question) : Matcher;

    /**
     * 只要有answer, 不管上文有没有命中过.
     *
     * @param Action|callable $action   $message could be Answer
     * @return static
     */
    public function isAnswer(callable $action = null) : Matcher;


    /**
     * 之前提了一个问题, 答案命中了问题的一个建议的情况.
     * 可以与 answered 挑选使用.
     *
     * @param int|string $suggestionIndex
     * @param Action|callable $action   $message could be Choice
     * @return static
     */
    public function isChoice(
        $suggestionIndex,
        callable $action = null
    ) : Matcher;

    /**
     * 有多个choice 中的一个
     * @param int[] $choices
     * @param callable|null $action
     * @return static
     */
    public function hasChoice(
        array $choices,
        callable $action = null
    ) : Matcher;

    /**
     * 尝试匹配一个临时定义的命令
     * 并把匹配成功的 CommandMessage 传递给interceptor
     *
     * @param string $signature
     * @param Action|callable $action  $message is CommandMessage
     * @return static
     */
    public function isCommand(
        string $signature,
        callable $action = null
    ) : Matcher;


    /**
     * 用php做比较脏的关键词检查.
     * 最好不要沦落到这一步.
     *
     * @param array $keyWords   [ 'word1', 'word2', ['synonym1', 'synonym2']]
     * @param Action|callable $action
     * @return static
     */
    public function hasKeywords(
        array $keyWords,
        callable $action = null
    ) : Matcher;


    /*------- feelings -------*/

    /**
     * 判断消息是否符合某种情感.
     *
     * to feel message match certain feeling
     * parser @see Feeling
     *
     * @param string $emotionName
     * @param Action|callable $action
     * @return static
     */
    public function feels(
        string $emotionName,
        callable $action = null
    ) : Matcher;

    /**
     * @param callable $action
     * @return static
     */
    public function isPositive(callable $action = null) : Matcher;

    /**
     * @param callable|null $action
     * @return static
     */
    public function isNegative(callable $action = null) : Matcher;

    /*------- intents -------*/

    /**
     * 只要存在任何命中意图, $session->getMatchedIntent()
     * 就会执行.
     *
     * @param callable|null $intentAction    $message is IntentMessage
     * @return static
     */
    public function isAnyIntent(
        callable $intentAction = null
    ) : Matcher;



    /**
     * 匹配单个意图. $session->getPossibleIntent($intentName) 如果存在
     * 如果没有 intentAction, 不会立刻执行.
     *
     * @param string $intentName  可以是意图的 ContextName, 也可以是意图的类名
     * @param callable|null $intentAction 匹配到的 IntentMessage 也将作为可依赖注入的参数, 可以直接用类名来依赖注入
     * @return static
     */
    public function isIntent(
        string $intentName,
        callable $intentAction = null
    ) : Matcher;


    /**
     * 如果匹配到了 $intentName
     * 但获取的 IntentMessage 实例可能没有获取所有必填的 Entities
     * 可以用这个方法来处理两种情形.
     *
     * @param string $intentName
     * @param callable|null $whenPrepared  获得的 IntentMessage::isPrepared() === true
     * @param callable|null $whenNotPrepared  获得的 IntentMessage::isPrepared() ===false
     * @return static
     */
    public function isPreparedIntent(
        string $intentName,
        callable $whenPrepared = null,
        callable $whenNotPrepared = null
    ) : Matcher;


    /**
     * 如果没有 intentAction, 不会立刻执行.
     *
     * @param array $intentNames
     * @param callable|null $intentAction
     * @return static
     */
    public function isIntentIn(
        array $intentNames,
        callable $intentAction = null
    ) : Matcher;


    /**
     * 是否匹配到了entity 类型
     *
     * @param string $entityName
     * @param callable|null $interceptor
     * @return static
     */
    public function hasEntity(
        string $entityName,
        callable $interceptor = null
    ) : Matcher;


    /**
     * 存在entity, 并且值 equals(==) $expect
     *
     * @param string $entityName
     * @param mixed $expect
     * @param callable|null $interceptor
     * @return static
     */
    public function hasEntityValue(
        string $entityName,
        $expect,
        callable $interceptor = null
    ) : Matcher;

    /*------- help -------*/

    /**
     * 遇到了用户寻求帮助, 则会执行 helping 的内容.
     *
     * help 默认的两种匹配方式
     * - is('mark')
     * - is(HelpInt)
     *
     * @see HelpInt
     *
     * @param callable|null $helping
     * @param string $mark  默认表示 help 的标记, 方便快速匹配. 默认是 ?
     * @return static
     */
    public function onHelp(callable $helping = null, string $mark = '?') : Matcher;


}