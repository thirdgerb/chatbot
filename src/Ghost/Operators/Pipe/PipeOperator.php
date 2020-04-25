<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Operators\Pipe;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;


/**
 * 管道式的算子. 允许一个结束了之后, 运行另一个.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class PipeOperator implements Operator
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
     * PipeOperator constructor.
     * @param Operator $current
     * @param Operator $next
     */
    public function __construct(Operator $current, Operator $next)
    {
        $this->current = $current;
        $this->next = $next;
    }


    public function invoke(Cloner $cloner): ? Operator
    {
        if (isset($this->current)) {
            $this->current = $this->current->invoke($cloner);
            return $this;
        }

        return $this->next;
    }


}