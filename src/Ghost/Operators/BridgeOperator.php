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

use Commune\Blueprint\Ghost\Operator\Finale;
use Commune\Blueprint\Ghost\Operator\Operator;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BridgeOperator implements Operator
{

    /**
     * @var Operator
     */
    protected $current;

    /**
     * @var callable
     */
    protected $next;

    /**
     * BridgeOperator constructor.
     * @param Operator $current
     * @param callable $next
     */
    public function __construct(Operator $current, callable $next)
    {
        $this->current = $current;
        $this->next = $next;
    }


    public function tick(): Operator
    {
        if ($this->current instanceof Finale) {
            return $this
                ->current
                ->getDialog()
                ->caller()
                ->operate($this->next);
        }

        $this->current = $this->current->tick();
        return $this;
    }

    public function isTicking(): bool
    {
        return $this->current->isTicking();
    }

    public function isTicked(): bool
    {
        return $this->current->isTicked();
    }

    public function ticked(): void
    {
        $this->current->ticked();
    }

    public function pushStack(callable $caller): void
    {
        $this->current->pushStack($caller);
    }

    public function dumpStack(): array
    {
        return $this->current->dumpStack();
    }

    public function getOperatorDesc(): string
    {
        return $this->current->getOperatorDesc();
    }


}