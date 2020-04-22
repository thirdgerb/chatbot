<?php


/**
 * Class BackStep
 * @package Commune\Ghost\Operators\Backward
 */

namespace Commune\Ghost\Operators\Backward;


use Commune\Blueprint\Ghost\Convo\Conversation;
use Commune\Blueprint\Ghost\Operator\Operator;

class BackStep implements Operator
{
    /**
     * @var int
     */
    protected $backStep;

    /**
     * BackStep constructor.
     * @param int $backStep
     */
    public function __construct(int $backStep)
    {
        $this->backStep = $backStep;
    }

    public function invoke(Cloner $cloner): ? Operator
    {
        $runtime = $cloner->runtime;
        $process = $runtime->getCurrentProcess();
        $id = $process->backStep($this->backStep);

        if (!isset($id)) {
            return new Rewind();
        }

        $process = $runtime->findProcess($id);

        if (!isset($process)) {
            return new Rewind();
        }

        $runtime->setCurrentProcess($process);
        return new Rewind();
    }


}