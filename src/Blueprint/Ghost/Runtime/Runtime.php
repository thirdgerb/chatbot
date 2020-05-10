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

use Commune\Blueprint\Ghost\Memory;
use Commune\Blueprint\Ghost\Ucl;
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


    /*------ context -------*/

    /**
     * 状态变更的消息, 用于和客户端同步.
     * 如果状态没有变更, 就没有消息.
     * @return ContextMsg|null
     */
    public function toContextMsg() : ? ContextMsg;

    /*------ memory -------*/

    /**
     * 获取或创建一个长程记忆单元
     *
     * @param string $id
     * @param bool $longTerm
     * @param array $defaults
     * @return Memory
     */
    public function findMemory(string $id, bool $longTerm, array $defaults) : Memory;

    /*------ save -------*/

    public function save() : void;
}