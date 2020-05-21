<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IRedirect;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsBaseDialog;
use Commune\Ghost\Dialog\IActivate\IRedirect;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IFulfill extends AbsBaseDialog
{
    /**
     * @var Ucl|null
     */
    protected $to;

    /**
     * @var array
     */
    protected $restoreStages;

    /**
     * @var int
     */
    protected $gcTurn;

    public function __construct(
        Cloner $cloner,
        Ucl $ucl,
        ? Ucl $to,
        array $stages,
        int $gcTurn
    )
    {
        $this->to = $to;
        $this->restoreStages = $stages;
        $this->gcTurn = $gcTurn;

        parent::__construct($cloner, $ucl);
    }


    protected function runInterception(): ? Dialog
    {
        return null;
    }

    protected function runTillNext(): Dialog
    {
        $process = $this->getProcess();

        $depending = $process->getDepending($this->ucl->getContextId());

        if (!empty($depending)) {
            foreach ($depending as $dependingUclStr) {
                $dependingUcl = $process->decodeUcl($dependingUclStr);
                $process->addBlocking(
                    $dependingUcl,
                    $dependingUcl->findContextDef($this->cloner)->getPriority()
                );
            }

            // 理论上到 blocking 就应该有结果.
            return $this->fallbackFlow($this, $process);
        }

        // 拦截回调的过程.
        if (isset($this->to)) {
            return new IRedirect($this->cloner, $this->to);
        }

        return $this->fallbackFlow($this, $process);
    }

    protected function selfActivate(): void
    {
        // unset
        $process = $this->getProcess();
        $process->unsetWaiting($this->ucl);

        if (!empty($this->restoreStages) && $this->gcTurn > 0) {
            $process->addDying($this->ucl, $this->gcTurn, $this->restoreStages);
        }
    }


}