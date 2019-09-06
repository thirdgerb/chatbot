<?php
/**
 * Created by PhpStorm.
 * User: BrightRed
 * Date: 2019/4/13
 * Time: 3:35 PM
 */

namespace Commune\Chatbot\Blueprint\Message\QA;

use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\Blueprint\Message\ReplyMsg;
use Commune\Chatbot\OOHost\Session\Session;

interface Question extends ReplyMsg
{
    /**
     * 所有的问题都可以有选项的概念.
     * confirmation 的选项是 yes 或 no
     * choose 的选项是 n个中的一个
     * 正常 ask 问题, 也可以有猜您想问, 或者没有.
     *
     * @return array
     */
    public function getSuggestions() : array;

    /**
     * @return string
     */
    public function getQuery() : string;


    /**
     * 默认值.
     * @return null|mixed
     */
    public function getDefaultValue();


    /**
     * default choice of suggestions
     * @return null|mixed
     */
    public function getDefaultChoice();

    /**
     * 查看一个消息是不是一个回答
     * 如果是回答, 包装成一个answer
     *
     * @param Session $session
     * @return Answer|null
     */
    public function parseAnswer(Session $session) : ? Answer;

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


}