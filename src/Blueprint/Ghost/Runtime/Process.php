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


/**
 * 对话进程.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $sessionId             进程所属的 Session
 * @property-read string $id                    进程的唯一 ID.
 *

 * # waiting
 * @property-read string|null $await            等待用户回复的任务.
 *
 * @property-read string[][] $watching          观察中的任务. 可以最先被触发 (watch)
 *  [ string $ucl => $watchingStageName[] ]
 *
 * @property-read int[] $blocking               阻塞中的任务. 有机会就抢占 (preempt)
 *  [ string $ucl => int $priority]
 *
 * @property-read string[][] $sleeping          睡眠中的任务. 可以fallback, 可以被指定Stage 唤醒(wake).
 *  [ string $ucl => $wakenStageName[] ]
 *
 * @property-read int[] $dying                  垃圾回收中的任务. 仍然可以被唤醒 (restore)
 *  [ string $id => int $gcTurns ]
 *
 * @property-read string[][] $yielding          等待中的任务. 只能被指定语境唤醒 (preempt)
 *  [ string $ucl  => string $Id ]
 *
 * @property-read string[] $depending           依赖中的任务. 被依赖对象唤醒.
 *  [ string $ucl => string $id]
 *
 *
 * ## history
 *
 * @property-read Process|null $prev            上一轮对话的进程实例.
 * @property-read string[] $backtrace           历史记录. 记录的是 await ucl
 * @property-read Ucl[] $forward                调用 "next" 时前进的方向. 如果没有, 则会fallback
 *
 * ## task
 *
 * @property-read Task[] $tasks                 缓存的 task
 *
 */
interface Process extends ArrayAndJsonAble
{

    /**
     * 构建 Router
     * @return Routing
     */
    public function buildRouter() : Routing;

    /*-------- alive ---------*/

    /**
     * @return Task
     */
    public function aliveTask() : Task;

    /**
     * @return Task
     */
    public function popAliveTask() : ? Task;

    /**
     * @param Task $task
     * @param bool $force
     * @return Task|null    如果挑战不成功, 或者两个 task 是相同的, 就返回 null
     */
    public function challengeTask(Task $task, bool $force = false) : ? Task;

    /*-------- task ---------*/

    /**
     * @param string $contextId
     * @return Task|null
     */
    public function popTask(string $contextId) : ? Task;

    /**
     * 获取或者创建一个 Task
     * @param Ucl $ucl
     * @return Task
     */
    public function getTask(Ucl $ucl) : Task;

    /*-------- block ---------*/

    public function block(Ucl $ucl) : void;

    public function popBlocking(string $id = null) : ? Task;

    /*-------- sleep ---------*/

    public function sleepTask(Task $task) : void;

    public function popSleeping(string $id = null) : ? Task;

    /*-------- watch ---------*/

    public function popWatching(string $id = null) : ? Task;

    /*-------- canceling ---------*/

    /**
     * @param string[] $ucl
     */
    public function addCanceling(array $ucl) : void;

    public function popCanceling() : ? string;

    public function flushCanceling() : void;

    /*-------- gc ---------*/

    public function addGc(Task $task) : void;

    /**
     * 清空掉需要 gc 的 Task
     */
    public function gc() : void;
}