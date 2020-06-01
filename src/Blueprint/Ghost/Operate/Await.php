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
use Commune\Protocals\HostMsg\Convo\QA\QuestionMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Await extends Finale
{

    /*-------- question ---------*/

    /**
     * @param string $query
     * @param array $suggestions
     * @param $defaultChoice
     * @return Operator
     */
    public function askChoose(
        string $query,
        array $suggestions = [],
        $defaultChoice = null
    ) : Operator;

    /**
     * @param string $query
     * @param bool $default
     * @param Ucl|string|null $positiveRoute
     * @param Ucl|string|null $negativeRoute
     * @return Operator
     */
    public function askConfirm(
        string $query,
        bool $default = true,
        $positiveRoute = null,
        $negativeRoute = null
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