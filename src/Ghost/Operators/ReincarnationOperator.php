<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Ending;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Runtime\Trace;
use Commune\Ghost\OperatorsBack\End\Await;


/**
 * 轮回 Operator.
 * 执行一轮之后如果终点是 Await, 还会执行下一轮.
 * 这样很可能打破算子轮次的上限.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ReincarnationOperator implements Operator
{
    /**
     * @var Operator|null
     */
    protected $current;

    /**
     * @var Operator
     */
    protected $next;

    /**
     * @var Trace
     */
    protected $trace;

    public function __construct(Trace $trace, Operator $current, Operator $next)
    {
        $this->trace = $trace;
        $this->current = $current;
        $this->next = $next;
    }

    public function invoke(Cloner $cloner): Operator
    {
        $operator = $this->current;

        while (true) {
            $this->trace->record($operator);

            if ($operator instanceof Await) {
                $operator->invoke($cloner);
                break;
            }

            if ($operator instanceof Ending) {
                return $operator;
            }

            $operator = $operator->invoke($cloner);
        }

        return $this->next;
    }


}