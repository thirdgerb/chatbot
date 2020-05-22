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
use Commune\Ghost\Dialog\AbsDialog;
use Commune\Ghost\Dialog\IActivate;
use Commune\Blueprint\Ghost\Runtime\Process;

/**
 * 允许当前语境执行 fallback 流程.
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @mixin AbsDialog
 *
 */
trait TFallbackFlow
{

    protected function fallbackFlow(Process $process) : Dialog
    {
        return $this->fallbackCallbacking($process)
            ?? $this->fallbackBlocking($process)
            ?? $this->fallbackSleeping($process)
            ?? $this->quitWatching($process)
            ?? $this->closeSession();
    }


    protected function fallbackBlocking(Process $process) : ? Dialog
    {
        // 检查 block
        $blocking = $process->popBlocking();

        if (!isset($blocking)) {
            return null;
        }

        return new IActivate\IPreempt($this->cloner, $blocking, $this->dumpStack());
    }
}