<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Stage;

use Commune\Framework\Blueprint\Command\CommandMsg;
use Commune\Ghost\Blueprint\Callables\Prediction;
use Commune\Message\Blueprint\ArrayMsg;
use Commune\Message\Blueprint\IntentMsg;
use Commune\Support\SoundLike\SoundLikeInterface;
use Illuminate\Support\Collection;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Matcher
{

    /*------- 匹配事件 -------*/

    /**
     * 如果不主动拦截, 则event 消息都会被忽视.
     * @param string $eventName
     * @return bool
     */
    public function isEvent(string $eventName) : bool;

    /**
     * @param string[] $eventName
     * @return bool
     */
    public function isEventIn(array $eventName) : bool;


    /*------- php matcher -------*/

    /**
     * 自定义的监听.
     * 用一个 prediction callable 判断是否命中条件.
     * 命中后执行 interceptor
     *
     * run action only if expecting prediction return true
     *
     * @param Prediction|callable $prediction  return bool
     * @return bool
     */
    public function expect(callable $prediction) : bool;


    /**
     * 检查 message 的 trimmedText 是否等于目标字符串. 不区分大小写, 精确匹配
     *
     * @param string $text
     * @return bool
     */
    public function is(string $text) : bool;


    /**
     * Message->isEmpty() === true
     *
     * @return bool
     */
    public function isEmpty() : bool;

    /**
     * 通过正则匹配获取数据.
     * 正则按顺序获取的参数, 会分配到一个数组中, 键名依次用 $keys 的值
     * 匹配后的结果作为 ArrayMessage 传递给 $action
     * 此外, 也可以用 matchEntity() 方法调用 php 实现的EntityExtractor, 原理类似敏感词匹配.
     *
     * 最好不要用这种方法. 而是依赖 NLU 去匹配.
     *
     * @param string $pattern  查询的正则
     * @param string[] $keys 查询到的正则匹配的参数.
     *
     * @return ArrayMsg|null
     */
    public function pregMatch(string $pattern, array $keys = []): ArrayMsg;

    /**
     * 判断输入信息是否是口头或文字的.
     *
     * 相当于 instanceOf(Verbal::class)
     *
     */
    public function isVerbal() : bool;

    /**
     * 判断传入的 message 是否是某个 MessageSubClass 的实例.
     *
     * @param string $messageClazz
     * @return bool
     */
    public function isInstanceOf(string $messageClazz) : bool;

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
     * @param callable|null $action
     * @return null|Collection
     */
    public function matchEntity(
        string $entityName,
        callable $action = null
    ) : ? Collection;


    /*------- question matcher -------*/

    /**
     * 只要有answer, 不管上文有没有命中过.
     *
     * @param string $answer
     * @return bool
     */
    public function isAnswer(string $answer) : bool;


    /**
     * 之前提了一个问题, 答案命中了问题的一个建议的情况.
     * 可以与 answered 挑选使用.
     *
     * @param int|string $suggestionIndex
     * @return bool
     */
    public function isChoice($suggestionIndex) : bool;

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
     * @return null|Collection
     */
    public function hasKeywords(array $keyWords) : ? Collection;


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
     * @return bool
     */
    public function isPositive() : bool;

    /**
     * @return bool
     */
    public function isNegative() : bool;

    /*------- intents -------*/

    /**
     * 只要存在任何命中意图
     * 就会执行.
     *
     * @return IntentMsg|null
     */
    public function isAnyIntent() : ? IntentMsg ;

    /**
     * 匹配单个意图. $session->getPossibleIntent($intentName) 如果存在
     * 如果没有 intentAction, 不会立刻执行.
     *
     * @param string $intentName  可以是意图的 ContextName, 也可以是意图的类名
     * @return IntentMsg|null
     */
    public function isIntent(string $intentName) : ? IntentMsg;

    /**
     * 如果没有 intentAction, 不会立刻执行.
     *
     * @param array $intentNames
     * @return IntentMsg|null
     */
    public function isIntentIn(array $intentNames) : ? IntentMsg;


    /**
     * 仅仅从 Conversation::getNlu() 对象中判断意图是否存在
     * 不需要定义 IntentMessage 对象.
     *
     * @param string $intentName
     * @return bool
     */
    public function hasPossibleIntent(string $intentName) : bool;

    /**
     * 是否匹配到了entity 类型
     *
     * @param string $entityName
     * @return bool
     */
    public function hasEntity(string $entityName) : bool;


    /**
     * 存在entity, 并且值 equals(==) $expect
     *
     * @param string $entityName
     * @param mixed $expect
     * @return bool
     */
    public function hasEntityValue(string $entityName, $expect) : bool;

    /*------- help -------*/

    /**
     * 遇到了用户寻求帮助, 则会执行 helping 的内容.
     *
     * help 默认的两种匹配方式
     * - is('mark')
     * - is(HelpInt)
     *
     * @param string $mark  默认表示 help 的标记, 方便快速匹配. 默认是 ?
     * @return bool
     */
    public function onHelp(string $mark = '?') : bool;


}