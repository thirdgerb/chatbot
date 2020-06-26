<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\RuntimeDriver;

use Commune\Blueprint\Exceptions\IO\InvalidSavedDataException;
use Commune\Blueprint\Ghost\Memory\Memory;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Contracts\Ghost\RuntimeDriver;

/**
 * Runtime Driver 的一个通用模型
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class ARuntimeDriver implements RuntimeDriver
{

    abstract protected function getProcessKey(string $clonerId, string $belongsTo) : string;

    abstract protected function doCacheProcess(string $key, Process $process, int $expire) : bool;


    abstract protected function doFetchProcess(string $key) : ? Process;


    abstract protected function getSessionMemoriesCacheKey(string $cloneId, string $convoId) : string;

    abstract protected function doCacheSessionMemories(string $key, array $map, int $expire) : bool;

    abstract protected function doFetchSessionMemory(string $key, string $memoryId) : ? string;

    public function cacheProcess(string $cloneId, string $convoId, Process $process, int $expire): bool
    {
        $key = $this->getProcessKey($cloneId, $convoId);
        return $this->doCacheProcess($key, $process, $expire);
    }

    public function fetchProcess(string $cloneId, string $convoId): ? Process
    {
        $key = $this->getProcessKey($cloneId, $convoId);
        return $this->doFetchProcess($key);
    }

    public function cacheSessionMemories(
        string $cloneId,
        string $convoId,
        array $memories,
        int $expire
    ): bool
    {
        $key = $this->getSessionMemoriesCacheKey($cloneId, $convoId);
        $saving = [];

        foreach ($memories as $memory) {
            $id = $memory->getId();
            /**
             * @var Memory $memory
             */
            $saving[$id] = serialize($memory);
        }

        return $this->doCacheSessionMemories($key, $saving, $expire);
    }

    public function fetchSessionMemory(string $cloneId, string $convoId, string $memoryId): ? Memory
    {
        $key = $this->getSessionMemoriesCacheKey($cloneId, $convoId);
        $data = $this->doFetchSessionMemory($key, $memoryId);

        if (empty($data)) {
            return null;
        }

        $memory = unserialize($data);
        if ($memory instanceof Memory) {
            return $memory;
        } else {
            $error = "cached session memory cannot unserialized, key $key";
            throw new InvalidSavedDataException($error);
        }
    }
}