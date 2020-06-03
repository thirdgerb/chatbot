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
use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Protocals\HostMsg\Convo\QA\QuestionMsg;


/**
 * 对话进程.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $belongsTo             进程所属的 Session
 * @property-read string $id                    进程的唯一 ID.
 *
 * @property-read Process|null $prev
 *
 *
 * @property-read Waiter|null $waiter
 * @property-read array[] $sleeping
 * @property-read array[] $dying
 * @property-read string[] $depending
 * @property-read string[] $callbacks
 * @property-read string[] $yielding
 * @property-read int[] $blocking
 */
interface Process extends ArrayAndJsonAble
{

    public function nextSnapshot(string $id, int $maxBacktrace) : Process;

    public function getTask(Ucl $ucl) : Task;

    public function getTaskById(string $contextId) : ? Task;

    public function activate(Ucl $ucl) : void;

    /*-------- context ---------*/

    public function getContextUcl(string $contextId) : ? Ucl;

    /*-------- status ---------*/

    /**
     * Process 本身是新创建的.
     * @return bool
     */
    public function isFresh() : bool;

    /*-------- await ---------*/

    public function getWaiter() : ? Waiter;

    /**
     * @param Ucl $ucl
     * @param QuestionMsg|null $question
     * @param Ucl[] $routes
     */
    public function await(
        Ucl $ucl,
        ? QuestionMsg $question,
        array $routes
    ) : void;

    /**
     * @return Ucl|null
     */
    public function getAwait() : ? Ucl;

    /**
     * @return QuestionMsg|null
     */
    public function getAwaitQuestion() : ? QuestionMsg;

    /**
     * @return Ucl[]
     */
    public function getAwaitRoutes() : array;

    /*-------- root ---------*/

    public function getRoot() : Ucl;

    /*-------- blocking ---------*/

    public function addBlocking(Ucl $ucl, int $priority) : void;

    public function firstBlocking() : ? Ucl;

    public function eachBlocking() : \Generator;

    /*-------- sleeping ---------*/

    public function addSleeping(Ucl $ucl, array $wakenStages) : void;

    public function firstSleeping() : ? Ucl;

    public function eachSleeping() : \Generator;

//    /*-------- yield ---------*/
//
//    public function addYielding(Ucl $ucl, string $id) : void;
//
//    public function eachYielding() : \Generator;
//
//    /*-------- dying ---------*/
//
    public function addDying(Ucl $ucl, int $turns = 0, array $restoreStages = []);

    /*-------- depending ---------*/

    public function getDepended(string $contextId) : ? Ucl;

    /**
     * @param Ucl $ucl
     * @param string $dependedContextId
     */
    public function addDepending(Ucl $ucl, string $dependedContextId) : void;

    /**
     * @param string $dependedContextId
     * @return Ucl[]
     */
    public function getDepending(string $dependedContextId) : array;

    /*-------- callback ---------*/

    public function addCallback(Ucl ...$callbacks) : void;

    public function firstCallback() : ? Ucl;

    public function eachCallbacks() : \Generator;


    /*-------- waiting ---------*/

    public function flushWaiting() : void;

    /*-------- backStep ---------*/

    public function backStep(int $step) : bool;

    /*-------- gc ---------*/

    public function gc() : void;
}