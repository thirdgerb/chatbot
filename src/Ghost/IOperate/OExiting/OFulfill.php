<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\IOperate\OExiting;

use Commune\Ghost\IOperate\AbsOperator;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\IOperate\Flows\FallbackFlow;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class OFulfill extends AbsOperator
{
    /**
     * @var int
     */
    protected $turns;

    /**
     * @var string[]
     */
    protected $restoreStages;

    protected function toNext(): Operator
    {
        $process = $this->dialog->process;
        $contextId = $this->dialog->ucl->getContextId();
        $depending = $process->getDepending($contextId);

        $process->addCallback(...$depending);
        $process->addDying($this->dialog->ucl, $this->turns, $this->restoreStages);

        return new FallbackFlow($this->dialog);
    }



}