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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Blueprint\Ghost\Operate\Finale;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRewind extends AbsDialog implements Finale
{

    /**
     * @var bool
     */
    protected $silent;

    public function __construct(
        Cloner $cloner,
        Ucl $ucl,
        Dialog $prev,
        bool $silent = false
    )
    {
        $this->silent = $silent;
        parent::__construct($cloner, $ucl, $prev);
    }

    protected function runTillNext(): Operator
    {
        $process = $this->getProcess();
        $prev = $process->prev;

        if (isset($prev)) {
            $this->setProcess($process);
        }

        $this->runAwait($this->silent);

        $this->ticked = true;
        return $this;
    }

}