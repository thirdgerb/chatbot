<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\IActivate;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Dialog\AbsBaseDialog;
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Blueprint\Ghost\Dialog\Activate\Depend;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IDepend extends AbsDialog implements Depend
{

    /**
     * @var Ucl
     */
    protected $depending;

    public function __construct(
        Cloner $cloner,
        Ucl $ucl,
        Ucl $depending,
        AbsBaseDialog $prev
    )
    {
        $this->depending = $depending;
        parent::__construct($cloner, $ucl, $prev);
    }

    protected function runTillNext(): Dialog
    {
        // 建立依赖关系
        $process = $this->getProcess();

        $process->addDepending($this->depending, $this->ucl->getContextId());
        $process->unsetWaiting($this->ucl);

        $stageDef = $this->ucl->findStageDef($this->cloner);
        return $stageDef->onActivate($this);
    }


}