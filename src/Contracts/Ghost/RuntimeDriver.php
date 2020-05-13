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

use Commune\Blueprint\Ghost\Cloner\ClonerScope;
use Commune\Blueprint\Ghost\Memory\Memory;
use Commune\Blueprint\Ghost\Runtime\Process;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface RuntimeDriver
{
    public function cacheProcess(string $sessionId, Process $process, int $expire) : bool;

    public function fetchProcess(string $sessionId) : ? Process;

    public function cacheSessionMemories(
        string $sessionId,
        array $memories,
        int $expire
    ) : bool;

    public function fetchSessionMemories(string $sessionId) : array;

    public function saveLongTermMemories(
        ClonerScope $scope,
        array $memories
    ) : bool;

    public function findLongTermMemories(string $memoryId) : ? Memory;

}