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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Await extends Finale
{

    /*-------- question ---------*/

    /**
     * 添加参数供后续的问题使用.
     *
     * @param array $slots
     * @return Await
     */
    public function withSlots(array $slots) : Await;

    /**
     * @param string $query
     * @param string[]|Ucl[] $suggestions
     * @param int|string $defaultChoice
     * @param Ucl[] $routes
     * @return Operator
     */
    public function askChoose(
        string $query,
        array $suggestions = [],
        $defaultChoice = null,
        array $routes = []
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
     * @param QuestionMsg $question
     * @return Operator
     */
    public function ask(
        QuestionMsg $question
    ) : Operator;
}