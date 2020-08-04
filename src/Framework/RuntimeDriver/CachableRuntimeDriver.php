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

use Commune\Contracts\Cache;
use Psr\Log\LoggerInterface;
use Commune\Framework\Spy\SpyAgency;
use Commune\Blueprint\Ghost\Runtime\Process;
use Commune\Blueprint\Ghost\Cloner\ClonerLogger;
use Commune\Blueprint\Exceptions\IO\InvalidSavedDataException;



/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
abstract class CachableRuntimeDriver extends ARuntimeDriver
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
     * TestCacheRuntimeDriver constructor.
     * @param Cache $cache
     * @param ClonerLogger $logger
     */
    public function __construct(
        Cache $cache,
        ClonerLogger $logger
    )
    {
        $this->cache = $cache;
        $this->logger = $logger;
        SpyAgency::incr(static::class);
    }


    protected function getProcessKey(string $clonerId, string $convoId) : string
    {
        return "clone:$clonerId:proc:of:$convoId";

    }

    protected function doCacheProcess(string $key, Process $process, int $expire) : bool
    {
        if ($expire !== 0) {
            return $this->cache->set(
                $key,
                gzcompress(serialize($process)),
                $expire
            );
        } else {
            $this->cache->forget($key);
            return true;
        }
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

    protected function getSessionMemoriesCacheKey(string $cloneId, string $convoId) : string
    {
        return "clone:$cloneId:cv:$convoId:mem";
    }

    /**
     * @param string $key
     * @param string[] $map
     * @param int $expire
     * @return bool
     */
    protected function doCacheSessionMemories(string $key, array $map, int $expire) : bool
    {
        if ($expire !== 0) {
            return $this->cache->hMSet($key, $map, $expire);
        } else {
            $this->cache->forget($key);
            return true;
        }
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

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }
}