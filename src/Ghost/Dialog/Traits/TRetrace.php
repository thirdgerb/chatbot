<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog\Traits;

use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Ghost\Dialog\AbsDialogue;
use Commune\Ghost\Dialog\DialogHelper;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @mixin AbsDialogue
 */
trait TRetrace
{
    protected function fallbackFlow(Dialog $from, Process $process) : Dialog
    {
        return $this->fallbackBlocking($from, $process)
            ?? $this->fallbackSleeping($from, $process)
            ?? $this->withdrawWatching($from, $process, Dialog\Withdraw\Quit::class)
            ?? DialogHelper::newDialog(
                $from,
                $this->ucl,
                Dialog\Finale\CloseSession::class
            );
    }

    protected function fallbackBlocking(Dialog $prev, Process $process) : ? Dialog
    {
        // 检查 block
        $blocking = $process->popBlocking();
        if (isset($blocking)) {
            return null;
        }
        $blockingUcl = $process->decodeUcl($blocking);
        return DialogHelper::newDialog(
            $prev,
            $blockingUcl,
            Dialog\Receive\Preempt::class
        );
    }
    
    protected function fallbackSleeping(Dialog $prev, Process $process) : ? Dialog
    {
        // 检查 sleeping
        $sleeping = $process->popSleeping();
        if (isset($sleeping)) {
            return null;
        }
        
        $sleepingUcl = $process->decodeUcl($sleeping);
        return DialogHelper::newDialog(
            $prev,
            $sleepingUcl,
            Dialog\Activate\Fallback::class
        );
    }


    protected function withdrawCanceling(Dialog $prev, Process $process) : ? Dialog
    {
        while ($canceling = $process->popCanceling()) {

            $cancelingUcl = $process->decodeUcl($canceling);

            $depending = $process->popDepending($cancelingUcl);
            if (!empty($depending)) {
                $process->addCanceling(array_map(
                    function(string $depending) use ($process) {
                        return $process->decodeUcl($depending);
                    },
                    $depending
                ));
            }

            /**
             * @var Dialog\Withdraw $next
             */
            $next = DialogHelper::newDialog(
                $prev,
                $cancelingUcl,
                Dialog\Withdraw\Cancel::class
            );

            $next = DialogHelper::withdraw($next);

            if (isset($next)) {
                return $next;
            }
        }

        return null;
    }

    protected function withdrawWatching(Dialog $prev, Process $process, string $type) : ? Dialog
    {
        while($watching = $process->popWatcher()) {
            /**
             * @var Dialog\Withdraw $next
             */
            $next = DialogHelper::newDialog(
                $prev,
                $process->decodeUcl($watching),
                $type
            );

            $next = DialogHelper::withdraw($next);
            if (isset($next)) {
                return $next;
            }
        }
        return null;
    }
    
    

    protected function resetRoot(Dialog $prev, Process $process, string $root) : Dialog
    {
        $rootUcl = $process->decodeUcl($root);
        return DialogHelper::newDialog(
            $prev,
            $rootUcl,
            Dialog\Activate::class
        );
    }

}