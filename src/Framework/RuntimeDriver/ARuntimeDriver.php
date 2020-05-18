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


    abstract protected function getSessionMemoriesCacheKey(string $cloneId, string $sessionId) : string;

    abstract protected function doCacheSessionMemories(string $key, array $map, int $expire) : bool;

    abstract protected function doFetchSessionMemories(string $key) : array;

    public function cacheProcess(string $clonerId, Process $process, int $expire): bool
    {
        $key = $this->getProcessKey($clonerId, $process->belongsTo);
        return $this->doCacheProcess($key, $process, $expire);
    }

    public function fetchProcess(string $clonerId, string $belongsTo): ? Process
    {
        $key = $this->getProcessKey($clonerId, $belongsTo);
        return $this->doFetchProcess($key);
    }

    public function cacheSessionMemories(
        string $clonerId,
        string $sessionId,
        array $memories,
        int $expire
    ): bool
    {
        $key = $this->getSessionMemoriesCacheKey($clonerId, $sessionId);
        $saving = [];

        foreach ($memories as $memory) {
            /**
             * @var Memory $memory
             */
            $saving[$memory->getId()] = serialize($memory);
        }

        return $this->doCacheSessionMemories($key, $saving, $expire);
    }

    public function fetchSessionMemories(string $clonerId, string $sessionId): array
    {
        $key = $this->getSessionMemoriesCacheKey($clonerId, $sessionId);
        $map = $this->doFetchSessionMemories($key);

        if (empty($map)) {
            return [];
        }

        $result = [];
        foreach ($map as $serialized) {
            $memory = unserialize($serialized);
            if ($memory instanceof Memory) {
                $result[$memory->getId()] = $memory;
            } else {
                $error = "cached session memory cannot unserialized, key $key";
                throw new InvalidSavedDataException($error);
            }
        }

        return $result;
    }
}