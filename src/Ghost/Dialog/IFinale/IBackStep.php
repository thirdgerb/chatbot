<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IFinale;

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Blueprint\Ghost\Dialog\Finale;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IBackStep extends AbsDialog implements Finale
{
    /**
     * @var int
     */
    protected $step;

    public function __construct(Cloner $cloner, Ucl $ucl, int $step, $stack = [])
    {
        if ($step <= 0) {
            throw new InvalidArgumentException("back step should greater than 0, $step given");
        }

        $this->step = $step;
        parent::__construct($cloner, $ucl, $stack);
    }

    protected function runTillNext(): Dialog
    {
        $this->ticked = true;
        return $this;
    }


    protected function selfActivate(): void
    {
        $this->runStack();
        $this->getProcess()->backStep($this->step);

        $this->runAwait(false);
    }


}