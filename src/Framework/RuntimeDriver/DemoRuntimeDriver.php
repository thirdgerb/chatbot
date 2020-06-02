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

use Commune\Blueprint\Ghost\Memory\Memory;
use Commune\Framework\FileCache\FileCacheOption;
use Commune\Framework\Spy\SpyAgency;
use Psr\Log\LoggerInterface;
use Commune\Contracts\Cache;
use Commune\Support\Registry\OptRegistry;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Cloner\ClonerLogger;
use Commune\Blueprint\Exceptions\IO\InvalidSavedDataException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class DemoRuntimeDriver extends ARuntimeDriver
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var OptRegistry
     */
    protected $registry;

    /**
     * TestCacheRuntimeDriver constructor.
     * @param Cache $cache
     * @param ClonerLogger $logger
     * @param OptRegistry $registry
     */
    public function __construct(Cache $cache, ClonerLogger $logger, OptRegistry $registry)
    {
        $this->cache = $cache;
        $this->logger = $logger;
        $this->registry = $registry;
        SpyAgency::incr(static::class);
    }

    protected function getProcessKey(string $clonerId, string $belongsTo) : string
    {
        return "clone:$clonerId:proc:of:$belongsTo";

    }

    protected function doCacheProcess(string $key, Process $process, int $expire) : bool
    {
        return $this->cache->set(
            $key,
            gzcompress(serialize($process)),
            $expire
        );
    }

    protected function doFetchProcess(string $key) : ? Process
    {
        $serialized = $this->cache->get($key);
        if (!isset($serialized)) {
            return null;
        }
        $unCompress = gzuncompress($serialized);

        $error = "cached process can not unserialized, key $key";

        if (false === $unCompress) {
            throw new InvalidSavedDataException($error);
        }

        $process = unserialize($unCompress);

        if ($process instanceof Process) {
            return $process;
        }

        throw new InvalidSavedDataException($error);
    }

    protected function getSessionMemoriesCacheKey(string $cloneId, string $sessionId) : string
    {
        return "clone:$cloneId:ss:$sessionId:mem";
    }

    /**
     * @param string $key
     * @param string[] $map
     * @param int $expire
     * @return bool
     */
    protected function doCacheSessionMemories(string $key, array $map, int $expire) : bool
    {
        return $this->cache->hMSet($key, $map, $expire);
    }

    /**
     * @param string $key
     * @param string $memoryId
     * @return string
     */
    protected function doFetchSessionMemory(string $key, string $memoryId) : ? string
    {
        return $this->cache->hGet($key, $memoryId);
    }


    public function saveLongTermMemories(
        string $clonerId,
        array $memories
    ): bool
    {
        $category = $this->registry->getCategory(FileCacheOption::class);

        foreach ($memories as $memory) {
            /**
             * @var Memory $memory
             */
            $id = $this->getSessionMemoriesCacheKey($clonerId, $memory->getId());
            $option = new FileCacheOption([
                'id' => $id,
                'serialized' => serialize($memory)
            ]);
            $category->save($option);
        }

        return true;
    }

    public function findLongTermMemories(string $clonerId, string $memoryId): ? Memory
    {
        $id = $this->getLongTermMemoryId($clonerId, $memoryId);
        $category = $this->registry->getCategory(FileCacheOption::class);
        if (!$category->has($id)) {
            return null;
        }

        /**
         * @var FileCacheOption $option
         */
        $option = $category->find($id);
        $serialized = $option->serialized;

        $memory = unserialize($serialized);
        if ($memory instanceof Memory) {
            return $memory;
        }

        return null;
    }

    protected function getLongTermMemoryId(string $clonerId, string $memoryId) : string
    {
        return "clone:$clonerId:memory:$memoryId";
    }

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }

}