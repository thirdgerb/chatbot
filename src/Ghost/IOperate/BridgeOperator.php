<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Finale;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Framework\Spy\SpyAgency;

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
     * @param \Closure $next
     */
    public function __construct(Operator $current, \Closure $next)
    {
        $this->current = $current;
        $this->next = $next;
        SpyAgency::incr(static::class);
    }

    public function tick(): Operator
    {
        if ($this->current instanceof Finale) {
            $next = call_user_func(
                $this->next,
                $this->getDialog()
            );

            return $next ?? $this->current;
        }

        $this->current = $this->current->tick();
        return $this;
    }

    public function getDialog(): Dialog
    {
        return $this->current->getDialog();
    }

    public function getName(): string
    {
        return $this->current->getName();
    }

    public function __invoke(): Operator
    {
        return $this;
    }

    public function isTicked(): bool
    {
        return $this->current->isTicked();
    }


    public function __destruct()
    {
        unset($this->current);
        unset($this->next);
        SpyAgency::decr(static::class);
    }

}