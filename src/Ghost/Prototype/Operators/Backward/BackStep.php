<?php


/**
 * Class BackStep
 * @package Commune\Ghost\Prototype\Operators\Backward
 */

namespace Commune\Ghost\Prototype\Operators\Backward;


use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;

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

    public function invoke(Conversation $conversation): ? Operator
    {
        $runtime = $conversation->runtime;
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