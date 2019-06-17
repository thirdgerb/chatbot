<?php
/**
 * Created by PhpStorm.
 * User: BrightRed
 * Date: 2019/4/13
 * Time: 3:35 PM
 */

namespace Commune\Chatbot\Blueprint\Message\QA;

use Commune\Chatbot\Blueprint\Message\Message;

interface Question extends Message
{
    /**
     * 所有的问题都可以有选项的概念.
     * confirmation 的选项是 yes 或 no
     * choose 的选项是 n个中的一个
     * 正常 ask 问题, 也可以有猜你想问, 或者没有.
     *
     * @return array
     */
    public function suggestions() : array;

    /**
     * 查看一个消息是不是一个回答
     * 如果是回答, 包装成一个answer
     *
     * @param Message $message
     * @return Answer|null
     */
    public function parseAnswer(Message $message) : ? Answer;

    /**
     * 如果一个问题已经被回答过 (parse answer)
     * answer 会记录下来.
     *
     * @return Answer|null
     */
    public function getAnswer() : ? Answer;

    /**
     * 当消息没有命中可选回答时
     * 如果有默认值, 则返回默认值.
     *
     * @return bool
     */
    public function isNullable() : bool;

    /**
     * 默认值.
     * @return null|mixed
     */
    public function getDefaultValue();

}