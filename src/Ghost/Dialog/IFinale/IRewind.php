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
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsBaseDialog;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Blueprint\Ghost\Dialog\Finale;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRewind extends AbsDialog implements Finale
{

    /**
     * @var bool
     */
    protected $silent;

    public function __construct(Cloner $cloner, Ucl $ucl, bool $silent = false, array $stacks)
    {
        $this->silent = $silent;
        parent::__construct($cloner, $ucl);
    }

    protected function runTillNext(): Dialog
    {
        $this->ticked = true;
        return $this;
    }

    protected function selfActivate(): void
    {
        $this->runStack();

        $process = $this->getProcess();
        $prev = $process->prev;
        $runtime = $this->cloner->runtime;
        if (isset($prev)) {
            $runtime->setCurrentProcess($process);
        }

        $this->runAwait($this->silent);
    }


}