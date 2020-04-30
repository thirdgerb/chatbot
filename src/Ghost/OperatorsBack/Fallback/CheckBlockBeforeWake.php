<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\OperatorsBack\Fallback;

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Operator\Operator;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Ghost\OperatorsBack\Current\RetainStage;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class CheckBlockBeforeWake implements Operator
{
    /**
     * @var Process
     */
    protected $process;

    /**
     * CheckBlockBeforeWake constructor.
     * @param Process $process
     */
    public function __construct(Process $process)
    {
        $this->process = $process;
    }


    public function invoke(Cloner $cloner): ? Operator
    {
        if (!$this->process->hasBlocking()) {
            return new FallbackToWake($this->process);
        }

        // 用 blocking 来替换掉当前的 thread
        $blocking = $this->process->popBlocking();
        $this->process->replaceAliveThread($blocking);

        // 重新唤醒流程.
        return new RetainStage();
    }


}