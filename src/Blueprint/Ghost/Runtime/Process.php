<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\Runtime;

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Support\Arr\ArrayAndJsonAble;


/**
 * 对话进程.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $belongsTo             进程所属的 Session
 * @property-read string $id                    进程的唯一 ID.
 *

 * # waiting
 * @property-read string|null $awaiting         等待用户回复的任务.
 *
 * @property-read Waiter|null $waiter
 *
 * @property-read string[][] $watching          观察中的任务. 可以最先被触发 (watch)
 *  [ string $ucl => '' ]
 *
 * @property-read int[] $blocking               阻塞中的任务. 有机会就抢占 (preempt)
 *  [ string $ucl => int $priority]
 *
 * @property-read string[][] $sleeping          睡眠中的任务. 可以fallback, 可以被指定Stage 唤醒(wake).
 *  [ string $ucl => $wakenStageName[] ]
 *
 * @property-read int[] $dying                  垃圾回收中的任务. 仍然可以被唤醒 (restore)
 *  [ string $id => [int $gcTurns, $stages[]  ]
 *
 * @property-read string[] $depending           依赖中的任务. 被依赖对象唤醒.
 *  [ string $ucl => string $id]
 *
 *
 * ## history
 *
 * @property-read string $root
 * @property-read Process|null $prev            上一轮对话的进程实例.
 * @property-read Waiter[] $backtrace           历史记录. 记录的是 await ucl
 *
 *
 * @property-read int[] $holding                语境相关的 ContextId
 */
interface Process extends ArrayAndJsonAble
{

    public function nextSnapshot(string $id, int $maxBacktrace) : Process;

    /*-------- alive ---------*/

    public function buildRoutes() : RoutesMap;

    public function setAwait(Waiter $waiter) : void;

    /*-------- ucl ---------*/

    public function getRoot() : Ucl;

    /**
     * @param string $ucl
     * @return Ucl
     * @throws InvalidArgumentException
     */
    public function decodeUcl(string $ucl) : Ucl;

    /*-------- watch ---------*/

    public function addWatcher(Ucl $watcher) : void;

    public function popWatcher() : ? Ucl;

    /*-------- block ---------*/

    public function addBlocking(Ucl $ucl, int $priority) : void;

    public function popBlocking(string $ucl = null) : ? Ucl;


    /*-------- sleep ---------*/

    public function addSleeping(Ucl $ucl, array $wakenStages) : void;

    public function popSleeping(string $ucl = null) : ? Ucl;

    /*-------- dying ---------*/

    public function addDying(Ucl $ucl, int $turns, array $restoreStages);

    /*-------- root ---------*/

    public function replaceRoot(Ucl $ucl) : void;

    /*-------- depending ---------*/

    public function addDepending(Ucl $ucl, string $dependedContextId) : void;

    public function hasDepending(string $dependedContextId) : bool;

    /**
     * @param string $contextId
     * @return array
     */
    public function popDepending(string $contextId) : array;

    /*-------- callback ---------*/

    public function addCallback(Ucl ...$ucls) : void;

    public function popCallback() : ? Ucl;

    /*-------- canceling ---------*/

    /**
     * @param Ucl[] $canceling
     */
    public function addCanceling(array $canceling) : void;

    /**
     * @return Ucl
     */
    public function popCanceling() : ? Ucl;

    /**
     * @return Ucl[]
     */
    public function dumpCanceling() : array;


    /*-------- waiting ---------*/

    public function unsetWaiting(Ucl $ucl) : void;

    public function flushWaiting();

    /*-------- path ---------*/

    public function resetPath(string $contextId) : void;

    public function insertPath(string $contextId, array $path) : void;

    public function shiftPath(string $contextId) : ? string;

    public function pathExists(string $contextId) : bool;

    /*-------- backStep ---------*/

    public function backStep(int $step) : bool;

    /*-------- gc ---------*/

    public function gc() : void;
}