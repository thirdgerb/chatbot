<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Tools;

use Commune\Blueprint\Ghost\Callables\Prediction;
use Commune\Protocals\Host\Convo\EventMsg;
use Commune\Protocals\Host\Convo\QuestionMsg;
use Commune\Protocals\Host\Convo\VerbalMsg;
use Commune\Support\Protocal\Protocal;
use Commune\Support\SoundLike\SoundLikeInterface;
use Illuminate\Support\Collection;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Matcher
{
    public function getMatchedParams() : array;

    public function isMatched() : bool;

    /*------- 匹配事件 -------*/

    /**
     * 如果不主动拦截, 则event 消息都会被忽视.
     * @param string $eventName
     * @return EventMsg|null
     */
    public function isEvent(string $eventName) : ?  EventMsg;

    /**
     * @param string[] $eventName
     * @return EventMsg|null
     */
    public function isEventIn(array $eventName) : ? EventMsg;


    /*------- php matcher -------*/

    /**
     * 自定义的监听.
     * 用一个 prediction callable 判断是否命中条件.
     * 命中后执行 interceptor
     *
     * run action only if expecting prediction return true
     *
     * @param Prediction|callable|string $prediction  return bool
     * @return bool
     */
    public function expect($prediction) : bool;


    /**
     * 检查 message 的 trimmedText 是否等于目标字符串. 不区分大小写, 精确匹配
     *
     * @param string $text
     * @return static
     */
    public function is(string $text) : Matcher;


    /**
     * Message->isEmpty() === true
     *
     * @return bool
     */
    public function isEmpty() : bool;

    /**
     * 通过正则匹配获取数据.
     *
     * 最好不要用这种方法. 而是依赖 NLU 去匹配.
     *
     * @param string $pattern  查询的正则
     * @return static
     */
    public function pregMatch(string $pattern): Matcher;

    /**
     * 判断输入信息是否是口头或文字的.
     *
     * 相当于 instanceOf(Verbal::class)
     *
     * @return VerbalMsg|null
     */
    public function isVerbal() : ? VerbalMsg;

    /**
     * 判断传入的 message 是否是某个 MessageSubClass 的实例.
     *
     * @param string $messageClazz
     * @return static
     */
    public function isInstanceOf(string $messageClazz) : Matcher;


    /**
     * 符合某个协议
     * @param string $protocalName
     * @return Protocal|null
     */
    public function isProtocal(string $protocalName) : ? Protocal;

    /**
     * 发音相似. 目前应该只支持中文.
     * 用于弥补其它系统对语音识别有限的问题.
     *
     * @param string $text
     * @param string $lang
     * @return bool
     */
    public function soundLike(
        string $text,
        string $lang = SoundLikeInterface::ZH
    ) : bool;

    /**
     * @param string $text
     * @param int $type
     * @param string $lang
     * @return bool
     */
    public function soundLikePart(
        string $text,
        int $type = SoundLikeInterface::COMPARE_ANY_PART,
        string $lang = SoundLikeInterface::ZH
    ) : bool;


    /**
     * 如果nlu没匹配上, 就用系统自带的 entity extractor 去匹配.
     * 通常就是关键词匹配算法.
     *
     * @param string $entityName
     * @return null|Collection
     */
    public function matchEntity(string $entityName) : ? Collection;




    /*------- question matcher -------*/

    /**
     * 输入是否是一个问题.
     * @return QuestionMsg|null
     */
    public function isQuestion() : ? QuestionMsg;

    /**
     * 只要有answer, 不管上文有没有命中过.
     *
     * @param string $answer
     * @return bool
     */
    public function isAnswer(string $answer) : bool;

    /**
     *
     * $matches = [ string $answer]
     *
     * @return static
     */
    public function isAnyAnswer() : Matcher;


    /**
     * 之前提了一个问题, 答案命中了问题的一个建议的情况.
     * 可以与 answered 挑选使用.
     *
     * @param int|string $suggestionIndex
     * @return static
     */
    public function isChoice($suggestionIndex) : Matcher;

    /**
     * 有多个choice 中的一个
     * @param array $choices
     * @return bool
     */
    public function hasChoiceIn(array $choices) : bool;

    /**
     * 尝试匹配一个临时定义的命令
     * 并把匹配成功的 CommandMessage 传递给interceptor
     *
     * @param string $signature
     * @return null|CommandMsg
     */
    public function isCommand(string $signature) : ? CommandMsg;


    /**
     * 用php做比较脏的关键词检查.
     * 最好不要沦落到这一步.
     *
     * @param array $keyWords   [ 'word1', 'word2', ['synonym1', 'synonym2']]
     * @return static
     */
    public function hasKeywords(array $keyWords) : Matcher;


    /*------- feelings -------*/

    /**
     * 判断消息是否符合某种情感.
     *
     * to feel message match certain feeling
     * parser @see Feeling
     *
     * @param string $emotionName
     * @return bool
     */
    public function feels(string $emotionName) : bool;

    /**
     * @return static
     */
    public function isPositive() : Matcher;

    /**
     * @return static
     */
    public function isNegative() : Matcher;

    /**
     * @return static
     */
    public function needHelp() : Matcher;

    /*------- intents -------*/

    /**
     * 匹配单个意图.
     *
     * @param string $intentName  可以是意图的 ContextName, 也可以是意图的类名
     * @return static
     */
    public function isIntent(string $intentName) : Matcher;

    /**
     * 在多个意图中匹配一个最近似的.
     *
     * @param string[] $intentNames (可以用 * 做通配符)
     * @return string|null
     */
    public function isIntentIn(array $intentNames) : ? string;

    /**
     * 任何可能的意图名
     *
     * @return string|null
     */
    public function isAnyIntent() : ? string;

    /**
     * 仅仅从 Cloner::getNlu() 对象中判断意图是否存在
     * 不需要定义 IntentMessage 对象.
     *
     * @param string $intentName
     * @return bool
     */
    public function hasPossibleIntent(string $intentName) : bool;

    /**
     * 检查 Intention 内是否匹配到了 Entity
     * 不会调用自己的检查机制.
     *
     * @param string $entityName
     * @return bool
     */
    public function hasEntity(string $entityName) : bool;


    /**
     * 存在entity, 并且值中包含有 $expect
     *
     * @param string $entityName
     * @param mixed $expect
     * @return bool
     */
    public function hasEntityValue(string $entityName, $expect) : bool;


}