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

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class BridgeOperator implements Operator
{
    /**
     * @var Operator
     */
    protected $prev;

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

            $next = call_user_func(
                $this->next,
                $this->current->getDialog()
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

    public function __invoke(): Operator
    {
        return $this;
    }


}