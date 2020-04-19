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

use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 对话进程.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $belongsTo         进程所属的父 ID
 * @property-read string $id                进程的唯一 ID. 每一轮请求的 ID 都不一样.
 * @property-read string $prevId
 * @property-read string[] $sleeping        sleeping Thread 的 id
 * @property-read string[] $blocking        blocking Thread 的 id
 * @property-read string[] $backtrace
 * @property-read Node $root                root Node
 */
interface Process extends ArrayAndJsonAble
{

    /*-------- 状态相关 --------*/


    /**
     * 当前运行中的进程.
     * @return Thread
     */
    public function aliveThread() : Thread;

    /**
     * @return string
     */
    public function aliveStageFullname() : string;


    /*-------- challenge --------*/

    /**
     * 尝试用 Blocking Thread 取代当前的 Alive Thread,
     * 通过比较 Thread 的 priority
     * 成功的话, 会把当前 Thread 踢出来
     *
     * @return Thread|null
     */
    public function challengeAliveThread() : ? Thread;

    /**
     * 替换掉当前的 Thread
     *
     * @param Thread $thread
     * @return Thread
     */
    public function replaceAliveThread(Thread $thread) : Thread;


    /**
     * @param Node|null $node
     */
    public function home(Node $node = null) : void;

    /*-------- 获取进程内的 Thread --------*/

    public function hasThread(string $threadId) : bool;

    public function getThread(string $threadId) : ? Thread;

    /*-------- sleeping --------*/

    /**
     * @param Thread $thread
     * @param bool $top             放在栈顶, 还是栈尾
     */
    public function addSleepingThread(Thread $thread, bool $top = true) : void;

    public function popSleeping(string $threadId = null) : ? Thread;


    /*-------- 保存 --------*/

    /**
     * 是否要保存
     * @return bool
     */
    public function shouldSave() : bool;

    /*-------- gc 相关 --------*/

    /**
     * 把一个 Thread 加入到 gc 行列中.
     * 在剩余的几个回合内, 这个 Thread 仍然有被唤醒的可能, 否则就死翘翘了
     *
     * @param Thread $thread
     * @param int $gcTurn
     */
    public function addGcThread(Thread $thread, int $gcTurn) : void;



    /*-------- snapshot 快照历史 --------*/

    /**
     * 生成一个新的快照
     * @return Process
     */
    public function nextSnapshot() : Process;

    /**
     * 上一步的进程.
     * @return Process|null
     */
    public function prev() : ? Process;

    /**
     * 对比两个 Process 的语境当前, 如果不相同, 则返回当前 Process 的语境.
     * @param Process $process
     * @return Node|null
     */
    public function compareContext(Process $process) : ? Node;

    /**
     * 返回若干步.
     * @param int $steps
     * @return string|null
     */
    public function backStep(int $steps) : ? string;

    /**
     * 进程栈快照的深度.
     * @return int
     */
    public function stepDepth() : int;

    /*---------- block ----------*/

    public function blockThread(Thread $thread) : void;

    /**
     * @return bool
     */
    public function hasBlocking() : bool;

    /**
     * @return Thread
     */
    public function popBlocking() : Thread;

}