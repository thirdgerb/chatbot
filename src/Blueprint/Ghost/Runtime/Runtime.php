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

use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Memory\Recollection;
use Commune\Protocals\Host\Convo\ContextMsg;

/**
 * 多轮对话的运行状态
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Trace $trace
 */
interface Runtime
{

    /*------ process -------*/

    /**
     * 获取当前的 Process
     * @return Process
     */
    public function getCurrentProcess() : Process;

    /**
     * 变更当前的 Process
     * @param Process $process
     */
    public function setCurrentProcess(Process $process) : void;

    /**
     * 使用 ContextName 生成一个新的 Process
     * @param string $contextName
     * @return Process
     */
    public function createProcess(string $contextName) : Process;

    /**
     * 通过 processId 获得一个已有的 Process
     * @param string $processId
     * @return Process|null
     */
    public function findProcess(string $processId) : ? Process;

    /*------ recollection -------*/

    /**
     * 通过唯一 Id 寻找记忆体
     * @param string $id
     * @return Recollection|null
     */
    public function findRecollection(string $id) : ? Recollection;

    /**
     * @param string $id
     * @param string $name
     * @param bool $longTerm
     * @param array $defaults
     * @return Recollection
     */
    public function createRecollection(
        string $id,
        string $name,
        bool $longTerm,
        array $defaults
    ) : Recollection;

    /**
     * 添加一个.
     * @param Recollection $recollection
     */
    public function addRecollection(Recollection $recollection) : void;


    /*------ context -------*/

    /**
     * 状态变更的消息, 用于和客户端同步.
     * 如果状态没有变更, 就没有消息.
     * @return ContextMsg|null
     */
    public function toContextMsg() : ? ContextMsg;

    /**
     * 缓存一个 Context 对象.
     * @param Context $context
     */
    public function cacheContext(Context $context) : void;

    /*------ yielding -------*/

    /**
     * 缓存一个 yielding 状态的 Thread
     * @param Thread $thread
     * @param int|null $ttl
     */
    public function setYielding(Thread $thread, int $ttl = null) : void;

    /**
     * 尝试寻找一个 Yielding 的 Thread
     * @param string $threadId
     * @return Thread|null
     */
    public function findYielding(string $threadId) : ? Thread;


    /*------ save -------*/

    public function save() : void;
}