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
     * @param string $cloneId
     * @param string $convoId
     * @param Process $process
     * @param int $expire
     * @return bool
     */
    public function cacheProcess(string $cloneId, string $convoId, Process $process, int $expire) : bool;

    /**
     * @param string $cloneId
     * @param string $convoId
     * @return Process|null
     */
    public function fetchProcess(string $cloneId, string $convoId) : ? Process;

    /**
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