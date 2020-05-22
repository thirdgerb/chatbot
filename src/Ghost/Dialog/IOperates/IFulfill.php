<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IOperates;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsBaseDialog;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Ghost\Dialog\Traits\TFallbackFlow;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IFulfill extends AbsDialog
{
    use TFallbackFlow;

    protected $gcTurns;

    protected $restoreStages;

    public function __construct(
        Cloner $cloner,
        Ucl $ucl,
        int $gcTurns,
        array $restoreStages,
        AbsBaseDialog $prev = null
    )
    {
        $this->gcTurns = $gcTurns;
        $this->restoreStages = $restoreStages;

        parent::__construct($cloner, $ucl, $prev);
    }

    protected function runTillNext(): Dialog
    {
        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl);

        // 允许复活.
        if ($this->gcTurns > 0) {
            $process->addDying($this->ucl, $this->gcTurns, $this->restoreStages);
        }

        $depending = $process->popDepending($this->ucl->getContextId());
        if (!empty($depending)) {
            $process->addCallback(...$depending);
        }

        return $this->fallbackFlow($process);
    }


}