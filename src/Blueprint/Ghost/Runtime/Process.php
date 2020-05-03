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
 * @property-read Process|null $prev            上一轮对话的进程实例.
 *
 * @property-read string[][] $watching          观察中的任务. 可以最先被触发.
 *  [ string $id => $watchingStageName[] ]
 *
 * @property-read string $aliveTaskId        正在运行中的任务.
 *
 * @property-read Waiter|null $waiter           当前对话的终态.
 * @property-read Waiter[] $backtrace           历史记录. 记录的是 waiter
 *
 * @property-read int[] $blocking               阻塞中的任务. 有机会就抢占.
 *  [ string $id => int $priority]
 *
 * @property-read string[][] $sleeping          睡眠中的任务. 可以被指定Stage 唤醒.
 *  [ string $id => $wakenStageName[] ]
 *
 * @property-read int[] $dying                  垃圾回收中的任务. 仍然可以被唤醒.
 *  [ string $id => int $gcTurns ]
 *
 * @property-read string[][] $yielding          等待中的任务. 只能被指定语境唤醒.
 *  [ string $id => string $dependingId ]
 *
 * @property-read string[] $depending           依赖中的任务. 被依赖对象唤醒.
 *  [ string $id => string $id]
 *
 * @property-read Task[] $tasks                 进行中的 Task 实例.
 *  [ string $id => Task $task]
 *
 */
interface Process extends ArrayAndJsonAble
{

    /**
     * 构建 Router
     * @return Router
     */
    public function buildRouter() : Router;

    /*-------- alive ---------*/

    /**
     * @return Task
     */
    public function aliveTask() : Task;

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

    public function getTask(string $contextId) : Task;

    /*-------- block ---------*/

    public function blockTask(Task $task) : void;

    public function popBlocking(string $id = null) : ? Task;

    /*-------- sleep ---------*/

    public function sleepTask(Task $task) : void;

    public function popSleeping(string $id = null) : ? Task;

    /*-------- watch ---------*/

    public function popWatching(string $id = null) : ? Task;

    /*-------- gc ---------*/

    public function addGc(Task $task) : void;

    /**
     * 清空掉需要 gc 的 Task
     */
    public function gc() : void;
}