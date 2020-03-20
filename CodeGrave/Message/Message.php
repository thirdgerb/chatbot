<?php

namespace Commune\Chatbot\Blueprint\Message;

use Carbon\Carbon;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 所有消息的抽象化定义.
 *
 * Interface Message
 * @package Commune\Chatbot\Blueprint\Message
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 又分为两大类:
 * - ConvoMessage 在 Conversation 中传递的 message
 * - Context, 作为上下文, 在 Session 中使用, 不可在Conversation里传递.
 *
 */
interface Message extends ArrayAndJsonAble
{

    // will trim when get trimmed text
    const TRIMMING_MARKS = " \t\n\r\0\x0B.,;";

    /**
     * 消息是不是意味着一个null.
     * 比如用户输入了几个空格, 或者某个字符串, 但实际上表示 null 的意思.
     *
     * @return bool
     */
    public function isEmpty() : bool ;

    /**
     * 消息的创建时间.
     * 消息可以不实时发送,
     * 通过signal 来指定一个发送时间.
     *
     * @return Carbon
     */
    public function getCreatedAt() : Carbon;

    /**
     * 投递时间. 为null 表示立刻可以投递.
     * @return null|Carbon
     */
    public function getDeliverAt() : ? Carbon;

    /**
     * 设置投递时间.
     * @param Carbon $carbon
     * @return static
     */
    public function deliverAt(Carbon $carbon) : Message;

    /**
     * 消息用文本表示.
     * 每种类型的消息都要能给出text
     *
     * @return string
     */
    public function getText() : string;

    /**
     * 清理过的text
     *
     * 1. 去掉两边多余的空字符
     * 2. 将全角转换为半角
     * 3. 未来还可能加入其它清理过程.
     *
     * @return string
     */
    public function getTrimmedText() : string ;

    /**
     * 用数组结构给出消息的核心数据.
     * 不包括 type 等公共信息.
     * 那部分数据在 toArray 里
     *
     * !!! 定义所有的 messages 时, 都需要考虑这个.
     *
     * @return array
     */
    public function toMessageData() : array;


    /**
     * 作为依赖注入对象时, 可以使用的依赖名
     * 依赖名的构成:
     *
     * 1. 当前类的类名
     * 2. Message
     * 3. 所有父类里的抽象类.
     * 4. 所有实现的 interface 里 Message 的子类.
     *
     * @return string[]
     */
    public function namesAsDependency() : array;
}