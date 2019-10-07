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
use Commune\Chatbot\Blueprint\Message\Tags\Conversational;
use Commune\Chatbot\OOHost\Session\Session;

/**
 * 与用户进行单轮对话的问题
 */
interface Question extends ReplyMsg, Conversational
{

    // 默认的 slots, 可以用于翻译或渲染
    const SLOT_QUERY = 'query';
    const SLOT_SUGGESTIONS = 'suggestions' ; //array
    const SLOT_DEFAULT_VALUE = 'defaultValue';
    const SLOT_DEFAULT_CHOICE = 'defaultChoice';
    const SLOT_SUGGESTION_STR = 'suggestionStr'; //implode suggestion with glue ","

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
     * 如果是回答, 通常包装成一个answer
     *
     * @param Session $session
     * @param Message|null $message  为null 则默认用 incomingMessage 做 parse 的对象.
     * @return Answer|null
     */
    public function parseAnswer(Session $session, Message $message = null) : ? Answer;

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