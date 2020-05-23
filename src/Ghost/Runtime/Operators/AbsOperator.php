<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Runtime\Operators;

use Commune\Blueprint\Exceptions\HostLogicException;
use Commune\Blueprint\Ghost\Operate\Operator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsOperator implements Operator
{
    /**
     * @var bool
     */
    protected $ticking = false;

    /**
     * @var bool
     */
    protected $ticked = false;

    public function tick(): Operator
    {
        // 每个 Dialog 实例只能 tick 一次.
        if ($this->ticked) {
            throw new HostLogicException(
                __METHOD__
                . ' try to tick dialog that ticked'
            );
        }

        if ($this->ticking) {
            throw new HostLogicException(
                __METHOD__
                . ' try to tick dialog that ticking'
            );
        }

        $this->ticking = true;

        // 尝试拦截.
        $next = $this->toNext();

        $this->ticking = false;
        $this->ticked = true;
        return $next;
    }

    abstract protected function toNext() : Operator;


    public function isTicking(): bool
    {
        return $this->ticking;
    }

    public function isTicked(): bool
    {
        return $this->ticked;
    }

    public function ticked(): void
    {
        $this->ticked = true;
    }


}