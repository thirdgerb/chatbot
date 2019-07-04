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
 */
interface Message extends ArrayAndJsonAble
{
    /**
     * 消息是不是意味着一个null.
     * 比如用户输入了几个空格, 或者某个字符串, 但实际上表示 null 的意思.
     *
     * @return bool
     */
    public function isEmpty() : bool ;

    /**
     * 消息的类型.
     * 每个消息都有一个独立的类型
     * 通常就是class name
     *
     * @return string
     */
    public function getMessageType() : string;

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
     * @return Message
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
     * 如果一个消息可以被视作一个命令,
     * 它就有一个去掉命令标识符的 c(om)m(an)d text
     *
     * 默认的命令标识符是 "#"
     * 例如 "#help -p" 的cmdText 是 "help -p"
     *
     * 这是方便定义 command 的时候不用关心标识符.
     * 同时, 匹配标识符的方法不需要执行多次.
     *
     * @return null|string
     */
    public function getCmdText() : ? string;

    /**
     * @param string|null $text
     */
    public function setCmdText(string $text = null) : void;

    /**
     * 用数组结构给出消息的核心数据.
     * 不包括 type 等公共信息.
     * 那部分数据在 toArray 里
     *
     * @return array
     */
    public function toMessageData() : array;


    /**
     * 作为依赖注入对象时, 可以使用的依赖名
     *
     * @return string[]
     */
    public function namesAsDependency() : array;
}