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

use Commune\Ghost\Dialog\AbsDialog;
use Commune\Ghost\Dialog\IActivate;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Runtime\Operator;
use Commune\Ghost\Dialog\IRetain;

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

    protected function fallbackFlow(Process $process) : ? Operator
    {
        return $this->fallbackToCallbacks($process)
            ?? $this->fallbackToBlocking($process)
            ?? $this->fallbackToSleeping($process);
    }

    protected function fallbackToCallbacks(Process $process) : ? Operator
    {
        $callback = $process->firstCallback();
        if (!isset($callback)) {
            return null;
        }

        return new IRetain\ICallback(
            $this->_cloner,
            $callback,
            $this
        );
    }

    protected function fallbackToBlocking(Process $process) : ? Operator
    {
        // 检查 block
        $blocking = $process->firstBlocking();

        if (!isset($blocking)) {
            return null;
        }

        return new IActivate\IPreempt(
            $this->cloner,
            $blocking,
            $this
        );
    }

    protected function fallbackToSleeping(Process $process) : ? Operator
    {
        $sleeping = $process->firstSleeping();

        if (!isset($sleeping)) {
            return null;
        }

        return new IRetain\IWake(
            $this->cloner,
            $sleeping,
            $this
        );
    }
}