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
use Commune\Blueprint\Ghost\Exceptions\BadOperationEndException;
use Commune\Blueprint\Ghost\Operator\Operator;

/**
 * 多米诺式的算子.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class FlowOperator implements Operator
{
    /**
     * 必须都是 DominoOperator
     * @var string[]
     */
    protected $domino = [];

    public function __construct(array $next = [])
    {
        $this->domino = array_merge($next, $this->domino);
    }

    abstract protected function doInvoke(Cloner $cloner) : ? Operator;

    public function invoke(Cloner $cloner): Operator
    {
        $newFlow = $this->doInvoke($cloner);

        // 产生了新的flow
        if (isset($newFlow)) {
            return $newFlow;
        }

        return $this->next();
    }

    protected function next() : Operator
    {
        $flow = $this->domino;
        $next = array_shift($flow);

        if (empty($next)) {
            throw new BadOperationEndException(
                static::class
                . ' got no ending'
            );
        }

        return new $next($flow);
    }

}