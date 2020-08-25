<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Operate;

use Commune\Blueprint\Ghost\Ucl;
use Commune\Protocals\HostMsg\Convo\QA\Confirm;
use Commune\Protocals\HostMsg\Convo\QA\QuestionMsg;
use Commune\Protocals\HostMsg\DefaultIntents;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Await extends Finale
{

    /*-------- question ---------*/

    /**
     * @param string $query
     * @param string[]|Ucl[] $suggestions
     * @param int|string $defaultChoice
     * @return Operator
     */
    public function askChoose(
        string $query,
        array $suggestions = [],
        $defaultChoice = null
    ) : Operator;

    /**
     * @param string $query
     * @param bool|null $default
     * @param null $positiveRoute
     * @param null $negativeRoute
     * @param string $positive
     * @param string $negative
     * @return Operator
     */
    public function askConfirm(
        string $query,
        ? bool $default = true,
        $positiveRoute = null,
        $negativeRoute = null,
        string $positive = Confirm::POSITIVE_LANG,
        string $negative = Confirm::NEGATIVE_LANG
    ) : Operator;

    /**
     * 要求一个文字回答.
     *
     * @param string $query
     * @param array $suggestions
     * @return Operator
     */
    public function askVerbal(
        string $query,
        array $suggestions = []
    ) : Operator;

    /**
     * 要求用户输入任何信息继续, 然后拿到一个值.
     * @param string $query
     * @param int $current
     * @param int $max
     * @param array $suggestions
     * @param null|string $next         表示继续的选项
     * @param null|string $break        表示跳出流程的选项.
     * @return Operator
     */
    public function askStepper(
        string $query,
        int $current,
        int $max,
        array $suggestions = [],
        ?string $next = DefaultIntents::GUEST_LOOP_NEXT,
        ?string $break = DefaultIntents::GUEST_LOOP_BREAK
    ) : Operator;


    /**
     * 用户的表达如果没有命中其它意图和路由 就命中默认选项
     *
     * @param string $query
     * @param array $suggestions
     * @param int $defaultChoice
     * @return Operator
     */
    public function askAny(
        string $query,
        array $suggestions,
        $defaultChoice
    ) ;

    /**
     * @param QuestionMsg $question
     * @return Operator
     */
    public function ask(
        QuestionMsg $question
    ) : Operator;

    /*-------- 属性操作 ---------*/


    /**
     * @return QuestionMsg|null
     */
    public function getCurrentQuestion() : ? QuestionMsg;

    /*-------- 预设的回调方法 ---------*/

    /**
     * 添加参数供后续的问题使用.
     *
     * @param array $slots
     * @return Await
     */
    public function withSlots(array $slots) : Await;


    /**
     * 为接下来要设置的  Question 添加回调方法.
     * @param callable $caller
     * @return Await
     */
    public function withQuestion(callable $caller) : Await;

}