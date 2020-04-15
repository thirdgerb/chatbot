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
 *
 * @property-read string|null $parentId
 * @property-read string|null $childId
 */
interface Process extends ArrayAndJsonAble
{

    /*-------- 状态相关 --------*/


    /**
     * 当前运行中的进程.
     * @return Thread
     */
    public function aliveThread() : Thread;

    public function aliveStage() : string;

    public function aliveStageFullname() : string;


    /*-------- challenge --------*/

    /**
     * 尝试将一个 Thread 取代当前的 Thread,
     * 通过比较 priority
     * 成功的话, 会把当前 Thread 踢出来
     *
     * @param Thread $thread
     * @param bool $force       强制取代
     * @return Thread|null
     */
    public function challenge(Thread $thread, bool $force = false) : ? Thread;


    /*-------- 获取进程内的 Thread --------*/

    public function hasThread(string $threadId) : bool;

    public function getThread(string $threadId) : ? Thread;
//
//    /*-------- sleeping --------*/
//
//    /**
//     * 遍历所有的 sleeping 进程
//     * @return Generator
//     */
//    public function eachSleeping() : Generator;
//
    /**
     * 将当前的 Thread 睡眠.
     * @param Thread $to
     * @return bool
     */
    public function sleepToThread(Thread $to) : bool;

    public function popSleeping(string $threadId = null) : ? Thread;

//
//    /*-------- block --------*/
//
//    /**
//     * 遍历所有的 sleeping Thread
//     * @return Thread[]
//     */
//    public function eachBlocked() : Generator;
//

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
    /*-------- gc 相关 --------*/

    /**
     * 把一个 Thread 加入到 gc 行列中.
     * 在剩余的几个回合内, 这个 Thread 仍然有被唤醒的可能, 否则就死翘翘了
     *
     * @param Thread $thread
     * @param int $gcTurn
     */
    public function addGc(Thread $thread, int $gcTurn) : void;


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

    /*---------- gc ----------*/

    /*---------- block ----------*/

    /**
     * @return bool
     */
    public function hasBlocking() : bool;

    /**
     * @return Thread
     */
    public function popBlocking() : Thread;

    /*---------- yielding ----------*/

    public function popYielding(string $threadId) : ? Thread;

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