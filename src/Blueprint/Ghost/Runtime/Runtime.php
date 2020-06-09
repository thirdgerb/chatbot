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

use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context;
use Commune\Blueprint\Ghost\Memory\Memory;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Protocals\HostMsg\Convo\ContextMsg;

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
     * 生成一个新的 Process
     * @param Ucl $root
     * @return Process
     */
    public function createProcess(Ucl $root) : Process;


    /*------ context -------*/

    /**
     * @param string $id
     * @return Context|null
     */
    public function getCachedContext(string $id) : ? Context;

    /**
     * @param Context $context
     */
    public function cacheContext(Context $context) : void;

    /**
     * 状态变更的消息, 用于和客户端同步.
     * 如果状态没有变更, 就没有消息.
     *
     * @param Cloner $cloner
     * @return ContextMsg|null
     */
    public function toChangedContextMsg() : ? ContextMsg;

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