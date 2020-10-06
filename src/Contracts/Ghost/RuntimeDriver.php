<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Contracts\Ghost;

use Commune\Blueprint\Ghost\Memory\Memory;
use Commune\Blueprint\Ghost\Runtime\Process;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RuntimeDriver
{

    /**
     * 保存 Process. 很明显, Process 是 Conversation 级别的.
     *
     * @param string $cloneSessionId
     * @param string $convoId
     * @param Process $process
     * @param int $expire
     * @return bool
     */
    public function cacheProcess(string $cloneSessionId, string $convoId, Process $process, int $expire) : bool;

    /**
     * 获取指定会话的 Conversation.
     *
     * @param string $cloneSessionId
     * @param string $convoId
     * @return Process|null
     */
    public function fetchProcess(string $cloneSessionId, string $convoId) : ? Process;

    /**
     * 保存所有的短期记忆. 显然短期记忆也是 Conversation 级别的.
     * 而不是 Session 级别的.
     *
     * @param string $cloneId
     * @param string $convoId
     * @param Memory[] $memories
     * @param int $expire
     * @return bool
     */
    public function cacheSessionMemories(
        string $cloneId,
        string $convoId,
        array $memories,
        int $expire
    ) : bool;

    /**
     * @param string $cloneId
     * @param string $convoId
     * @param string $memoryId
     * @return Memory|null
     */
    public function fetchSessionMemory(
        string $cloneId,
        string $convoId,
        string $memoryId
    ) : ? Memory;

    /**
     * 长程记忆是 Session 级别的, 相互之间共享.
     *
     * @param string $clonerId
     * @param Memory[] $memories
     * @return bool
     */
    public function saveLongTermMemories(
        string $clonerId,
        array $memories
    ) : bool;

    /**
     * @param string $clonerId
     * @param string $memoryId
     * @return Memory|null
     */
    public function findLongTermMemories(string $clonerId, string $memoryId) : ? Memory;

}