<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Runtime;

use Commune\Framework\Blueprint\Abstracted\Comprehension;
use Generator;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 对话进程.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $belongsTo     进程所属的父 ID
 * @property-read string $id            进程的唯一 ID. 每一轮请求的 ID 都不一样.
 * @property-read string[] $sleeping    sleep Thread 的 id
 * @property-read string[] $blocking    block Thread 的 id
 * @property-read string[] $watching
 *
 * @property-read string|null $parentId
 * @property-read string|null $childId
 */
interface Process extends ArrayAndJsonAble
{
//
//    /**
//     * @return bool
//     */
//    public function isAlive() : bool;
//

    /*-------- 状态相关 --------*/

//
//    /**
//     * 当前运行中的进程.
//     * @return Thread
//     */
//    public function aliveThread() : Thread;
//
//    public function aliveContext() : string;
//
//    public function aliveThreadId() : string;
//
    public function aliveStage() : string;

    public function aliveStageFullname() : string;
//
//    /*-------- sleeping --------*/
//
//    /**
//     * 遍历所有的 sleeping 进程
//     * @return Generator
//     */
//    public function eachSleeping() : Generator;
//
//    /**
//     * 将当前的 Thread
//     * @param Thread $thread
//     * @return bool
//     */
//    public function sleepThreadTo(Thread $thread) : bool;
//
//    /**
//     * 弄醒一个睡着的 Thread
//     * @return bool
//     */
//    public function wakeThread() : bool;
//
//    /*-------- block --------*/
//
//    /**
//     * 遍历所有的 sleeping Thread
//     * @return Thread[]
//     */
//    public function eachBlocked() : Generator;
//
    /**
     * 尝试将一个 Thread 阻塞当前进程中.
     *
     * @param Thread $thread
     * @return bool
     */
    public function blockThread(Thread $thread) : bool;
//
//    /**
//     * 是否被插入的 Thread 挡住了.
//     * @return bool
//     */
//    public function isBlocked() : bool;
//
//    /*-------- 保存 --------*/
//
//    /**
//     * 是否要保存
//     * @return bool
//     */
//    public function shouldSave() : bool;
//
//    /*-------- gc 相关 --------*/
//
//    /**
//     * 需要 gc 的 Thread
//     * @return Thread[]
//     */
//    public function gc() : array;
//
//
//    /*-------- snapshot 快照历史 --------*/
//
//    /**
//     * 上一步的进程. 只是获得实例, 不会设置为 alive
//     * @return Process|null
//     */
//    public function prev() : ? Process;
//
//    /**
//     * 返回若干步.
//     * @param int $steps
//     * @return Process|null
//     */
//    public function backStep(int $steps) : ? Process;
//
//    /**
//     * 进程栈快照的深度.
//     * @return int
//     */
//    public function stepDepth() : int;
//
//    /**
//     * 检查进程快照栈是否过长了, 如果过长了的话会去掉最深的一个.
//     * @param int $max
//     * @return bool
//     */
//    public function expireStep(int $max) : bool;

    /*---------- wait ----------*/

    /**
     * @param string[] $answers     answer => stageFullname
     * @param string[] $commands    command => stageFullname
     * @return mixed
     */
    public function wait(
        array $answers,
        array $commands
    );

    /**
     * @param Comprehension $comprehension
     * @return null|string                      stageFullName
     */
    public function hearCommand(Comprehension $comprehension) : ? string;

    /**
     * @param Comprehension $comprehension
     * @return null|string                      stageFullName
     */
    public function hearAnswer(Comprehension $comprehension) : ? string;
}