<?php


/**
 * Class Rewind
 * @package Commune\Ghost\Operators\Backward
 */

namespace Commune\Ghost\OperatorsBack\Backward;


use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;

/**
 * 重置当前对话, 返回上一轮的状态.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class Rewind implements Operator
{
    /**
     * @var bool $quiet
     */
    protected $quiet;

    /**
     * Rewind constructor.
     * @param bool $quiet
     */
    public function __construct(bool $quiet = false)
    {
        $this->quiet = $quiet;
    }


    public function invoke(Cloner $cloner): ? Operator
    {
        $process = $cloner->runtime->getCurrentProcess();
        $prev = $process->prev();

        if (isset($prev)) {
            $cloner->runtime->setCurrentProcess($prev);
        }

        $question = $prev->aliveThread()->getQuestion();
        if (!$this->quiet && isset($question)) {
            $cloner->output($question);
        }

        return null;
    }


}