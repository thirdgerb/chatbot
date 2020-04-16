<?php


/**
 * Class Rewind
 * @package Commune\Ghost\Prototype\Operators\Backward
 */

namespace Commune\Ghost\Prototype\Operators\Backward;


use Commune\Ghost\Blueprint\Convo\Conversation;
use Commune\Ghost\Blueprint\Operator\Operator;

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


    public function invoke(Conversation $conversation): ? Operator
    {
        $process = $conversation->runtime->getCurrentProcess();
        $prev = $process->prev();

        if (isset($prev)) {
            $conversation->runtime->setCurrentProcess($prev);
        }

        $question = $prev->aliveThread()->getQuestion();
        if (!$this->quiet && isset($question)) {
            $conversation->output($question);
        }

        return null;
    }


}