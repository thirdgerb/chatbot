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
use Commune\Ghost\OperatorsBack\Current\WakeStage;
use Commune\Ghost\OperatorsBack\End\QuitSession;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class FallbackToWake implements Operator
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
        $sleeping = $this->process->popSleeping();

        // 已经有 sleeping 的 Thread, 则可以走wake
        if (isset($sleeping)) {
            $this->process->replaceAliveThread($sleeping);
            return new WakeStage();
        }

        // 否则要走 quit
        return new QuitSession();
    }


}