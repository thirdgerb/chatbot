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

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Context\Dependable;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Blueprint\Ghost\Operate;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Ghost\IOperate\OExiting;
use Commune\Ghost\IOperate\OFinale;
use Commune\Ghost\IOperate\ORedirect;
use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * todo 考虑把所有 operator 都改造成依赖注入, 方便替换流程. 但必要性不大.
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
        if (isset($ifNone) && $ifNone === $this->_ucl->stageName) {
            throw new InvalidArgumentException(
                static::class . '::next should not pass self stage name as next path, which lead to endless loop'
            );
        }

        return new ORedirect\ONext($this, $ifNone);
    }

    public function rewind(bool $silent = false): Operator
    {
        return new OFinale\ORewind($this, $silent);
    }

    public function backStep(int $step = 1): Operator
    {
        return new ORedirect\OBackStep($this, $step);
    }

    public function close(): Operator
    {
        return new OFinale\OCloseSession($this);
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

    public function redirectTo(Ucl $ucl, bool $noBlocking = true): Operator
    {
        return new ORedirect\ORedirectTo($this, $ucl, $noBlocking);
    }

    public function reset(Ucl $root = null): Operator
    {
        return new ORedirect\OReset($this, $root);
    }

    public function dependOn(Dependable $dependable, string $fieldName = null): Operator
    {
        $dependUcl = $dependable->toFulfillUcl();
        $this->shouldNotBeSameContext(static::class . '::'. __FUNCTION__, $dependUcl);

        return new ORedirect\ODependOn($this, $dependable, $fieldName);
    }

    public function blockTo(Ucl $target, int $priority = null): Operator
    {
        $this->shouldNotBeSameContext(static::class . '::'. __FUNCTION__, $target);
        return new ORedirect\OBlockTo($this, $target, $priority);
    }

    protected function shouldNotBeSameContext(string $func, Ucl $target) : void
    {
        $target = $target->toInstance($this->cloner);
        if ($this->ucl->isSameContext($target)) {
            $targetUcl = $target->encode();
            $currentUcl = $this->ucl->encode();
            throw new InvalidArgumentException(
                "target ucl $targetUcl is in same context with current $currentUcl, called by $func"
            );
        }
    }

    public function sleepTo(Ucl $target, array $wakenStages = []): Operator
    {
        $this->shouldNotBeSameContext(static::class . '::'. __FUNCTION__, $target);
        return new ORedirect\OSleepTo($this, $target, $wakenStages);
    }

    public function yieldTo(
        string $sessionId,
        Context $target,
        Ucl $fallback = null,
        string $convoId = null
    ): Operator
    {
        $this->shouldNotBeSameContext(static::class . '::'. __FUNCTION__, $target->getUcl());
        if (isset($fallback)) {
            $this->shouldNotBeSameContext(static::class . '::'. __FUNCTION__, $fallback);
        }
        return new ORedirect\OYieldTo(
            $this,
            $sessionId,
            $target,
            $fallback,
            $convoId
        );
    }


    public function fulfill(
        array $restoreStage = [],
        int $gcTurns = 0
    ): Operator
    {
        return new OExiting\OFulfill($this, $gcTurns, $restoreStage);
    }

    public function confuse(bool $silent = false, $fallbackStrategy = null): Operator
    {
        return new OExiting\OConfuse($this, $silent, $fallbackStrategy);
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