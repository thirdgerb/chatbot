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

use Commune\Blueprint\Ghost\Ucl;
use Commune\Protocals\HostMsg\Convo\QuestionMsg;
use Commune\Support\Arr\ArrayAndJsonAble;


/**
 * 对话进程.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $belongsTo             进程所属的 Session
 * @property-read string $id                    进程的唯一 ID.
 *
 * @property-read Process|null $prev
 * @property-read Waiter|null $waiter
 *
 *
 */
interface Process extends ArrayAndJsonAble
{

    public function nextSnapshot(string $id, int $maxBacktrace) : Process;

    /*-------- context ---------*/

    public function isContextStatus(string $contextId, int $status) : bool;

    public function getContextStatus(string $contextId) : int;

    public function getContextUcl(string $contextId) : ? Ucl;

    /*-------- status ---------*/

    /**
     * Process 本身是新创建的.
     * @return bool
     */
    public function isFresh() : bool;

    /*-------- await ---------*/

    public function buildRoutes() : RoutesMap;

    public function await(
        Ucl $ucl,
        ? QuestionMsg $question,
        array $stageRoutes,
        array $contextRoutes
    ) : void;


    /**
     * @return string[]
     */
    public function getAwaitStageNames() : array;

    /**
     * @return Ucl[]
     */
    public function getAwaitContexts() : array;

    /*-------- ucl ---------*/

    public function getRoot() : Ucl;

    /*-------- wait ---------*/

    public function getAwaiting() : ? Ucl;

    /*-------- watch ---------*/

    public function addWatcher(Ucl $watcher) : void;

    public function popWatcher() : ? Ucl;

    /**
     * @return \Generator|Ucl[]
     */
    public function eachWatchers() : \Generator;

    /*-------- block ---------*/

    public function addBlocking(Ucl $ucl, int $priority) : void;

    public function firstBlocking() : ? Ucl;

    public function eachBlocking() : \Generator;

    /*-------- sleep ---------*/

    public function addSleeping(Ucl $ucl, array $wakenStages) : void;

    public function firstSleeping() : ? Ucl;

    public function eachSleeping() : \Generator;

    /*-------- yield ---------*/

    public function addYielding(Ucl $ucl, string $id) : void;

    public function eachYielding() : \Generator;

    /*-------- dying ---------*/

    public function addDying(Ucl $ucl, int $turns, array $restoreStages);


    /*-------- depending ---------*/

    public function getDependedBy(string $contextId) : ? Ucl;

    public function addDepending(Ucl $ucl, string $dependedDependedContextId) : void;

    /**
     * @param string $dependedContextId
     * @return array
     */
    public function dumpDepending(string $dependedContextId) : array;

    /*-------- callback ---------*/

    public function addCallback(Ucl ...$callbacks) : void;

    public function firstCallback() : ? Ucl;

    public function eachCallbacks() : \Generator;

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

    public function resetPath(Ucl $ucl, array $path) : void;

    public function insertPath(Ucl $ucl, array $path) : void;

    public function shiftPath(string $contextId) : ? string;

    public function pathExists(string $contextId) : bool;

    /*-------- backStep ---------*/

    public function backStep(int $step) : bool;

    /*-------- gc ---------*/

    public function gc() : void;
}