<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Dialog;

use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Ghost\Operate;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\IOperate\OExiting;
use Commune\Ghost\IOperate\OFinale;
use Commune\Ghost\IOperate\ORedirect;
use Commune\Ghost\IOperate\OSuspend;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class AbsDialog extends AbsBaseDialog
{

    public function await(
        array $stageRoutes = [],
        array $contextRoutes = [],
        int $expire = null
    ): Operate\Await
    {
        return new OFinale\OAwait(
            $this,
            $stageRoutes,
            $contextRoutes,
            $expire
        );
    }

    public function dumb(): Operator
    {
        return new OFinale\ODumb($this);
    }

    public function next(string $ifNone = null): Operator
    {
        return new ORedirect\ONext($this, $ifNone);
    }

    public function rewind(bool $silent = false): Operator
    {
        return new OFinale\ORewind($this, $silent);
    }

    public function backStep(int $step = 1): Operator
    {
        return new OFinale\OBackStep($this, $step);
    }


    public function goStage(string $stageName, string ...$stageNames): Operator
    {
        array_unshift($stageNames, $stageName);
        return new ORedirect\OGoStage($this, $stageNames);
    }

    public function reactivate(): Operator
    {
        return new ORedirect\OReactivate($this);
    }

    public function redirectTo(Ucl $ucl): Operator
    {
        return new ORedirect\ORedirectTo($this, $ucl);
    }

    public function reset(Ucl $root = null): Operator
    {
        return new ORedirect\OReset($this, $root);
    }

    public function dependOn(Ucl $dependUcl, string $fieldName = null): Operator
    {
        return new OSuspend\ODependOn($this, $dependUcl, $fieldName);
    }

    public function blockTo(Ucl $target, int $priority = null): Operator
    {
        return new OSuspend\OBlockTo($this, $target, $priority);
    }

    public function sleepTo(Ucl $target, array $wakenStages = []): Operator
    {
        return new OSuspend\OSleepTo($this, $target, $wakenStages);
    }


    public function fulfill(
        array $restoreStage = [],
        int $gcTurns = 0
    ): Operator
    {
        return new OExiting\OFulfill($this);
    }

    public function confuse(bool $silent = false): Operator
    {
        return new OExiting\OConfuse($this, $silent);
    }


    public function cancel(): Operator
    {
        return new OExiting\OCancel($this);
    }

    public function quit(): Operator
    {
        return new OExiting\OQuit($this);
    }

}