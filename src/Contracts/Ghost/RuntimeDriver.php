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
     * @param string $clonerId
     * @param Process $process
     * @param int $expire
     * @return bool
     */
    public function cacheProcess(string $clonerId, Process $process, int $expire) : bool;

    /**
     * @param string $clonerId
     * @param string $belongsTo
     * @return Process|null
     */
    public function fetchProcess(string $clonerId, string $belongsTo) : ? Process;

    /**
     * @param string $clonerId
     * @param string $sessionId
     * @param Memory[] $memories
     * @param int $expire
     * @return bool
     */
    public function cacheSessionMemories(
        string $clonerId,
        string $sessionId,
        array $memories,
        int $expire
    ) : bool;

    /**
     * @param string $clonerId
     * @param string $sessionId
     * @return Memory[]
     */
    public function fetchSessionMemories(string $clonerId, string $sessionId) : array;

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