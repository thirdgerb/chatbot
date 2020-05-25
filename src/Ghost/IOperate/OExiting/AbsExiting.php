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

use Generator;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\IOperate\AbsOperator;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Operate\Operator;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsExiting extends AbsOperator
{
    /**
     * @var Ucl[]
     */
    protected $canceling = [];

    abstract protected function doWithdraw(
        Process $process,
        Ucl $canceling
    ) : ? Operator;


    protected function addCanceling(Ucl ...$canceling) : void
    {
        $this->canceling = array_merge(
            $this->canceling,
            $canceling
        );
    }

    protected function popCanceling() : ? Ucl
    {
        return array_shift($this->canceling);
    }


    protected function recursiveWithdraw(Process $process) : ? Operator
    {
        while ($canceling = $this->popCanceling()) {

            $cancelingId = $canceling->getContextId();
            $allDepending = $process->getDepending($cancelingId);

            $this->addCanceling(...$allDepending);
            $next = $this->doWithdraw($process, $canceling);

            if (isset($next)) {
                return $next;
            }

            // 没有拦截的话, 就添加到 dying
            // 这样就能取消掉 depending 关系
            $process->addDying($canceling);
        }

        return null;
    }

    protected function restoreCanceling(Process $process, array $poppedDepends) : void
    {
        $uncanceled = $this->canceling;
        foreach ($uncanceled as $un) {
            $ucl = $un->toEncodedStr();
            $process->addDepending(
                $un,
                $poppedDepends[$ucl]
            );
        }
    }

    protected function quitBatch(Process $process, Generator $each) : ? Operator
    {
        foreach ($each as $ucl) {

            $this->addCanceling([$ucl]);
            $next = $this->recursiveWithdraw($process);
            if (isset($next)) {
                return $next;
            }
        }

        return null;
    }
}