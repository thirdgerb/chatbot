<?php


namespace Commune\Framework\Cache;

use Commune\Contracts\Cache;
use Psr\SimpleCache\CacheInterface;
use Commune\Support\RunningSpy\Spied;
use Commune\Blueprint\Framework\Session;
use Commune\Support\RunningSpy\SpyTrait;

/**
 * 模拟的cache. 方便测试用.
 */
class ArrayCache implements Cache, Spied
{
    use SpyTrait;

    protected static $cached = [];

    protected static $hashMap = [];

    protected $logger;

    /**
     * @var string
     */
    protected $traceId;

    /**
     * @var CacheInterface
     */
    protected $psrCache;

    public function __construct(Session $session)
    {
        $this->logger = $session->getLogger();
        $this->traceId = $session->getTraceId();
        self::addRunningTrace($this->traceId, $this->traceId);
    }


    public function set(string $key, string $value, int $ttl = null): bool
    {
        self::$cached[$key] = $value;
        return true;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, self::$cached);
    }

    public function get(string $key): ? string
    {
        return self::$cached[$key] ?? null;
    }


    public function lock(string $key, int $ttl = null): bool
    {
        return true;
    }

    public function forget(string $key): bool
    {
        $i = isset(self::$cached[$key]) || isset(self::$hashMap[$key]);
        unset(self::$cached[$key]);
        unset(self::$hashMap[$key]);
        return $i;
    }

    public function unlock(string $key): bool
    {
        return $this->forget($key);
    }

    public function getPSR16Cache(): CacheInterface
    {
        return $this->psrCache
            ?? $this->psrCache = new Psr16CacheAdapter($this);
    }

    public function getMultiple(array $keys, $default = null): array
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key) ?? $default;
        }
        return $results;
    }

    public function setMultiple(array $values, int $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set(strval($key), $value, $ttl);
        }
        return true;
    }

    public function delMultiple(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->forget($key);
        }
        return true;
    }

    public function expire(string $key, int $ttl): bool
    {
        return true;
    }

    public function hSet(string $key, string $memberKey, string $value, int $ttl = null): bool
    {
        static::$hashMap[$key][$memberKey] = $value;
    }

    public function hGet(string $key, string $memberKey): ? string
    {
        return static::$hashMap[$key][$memberKey] ?? null;
    }

    public function hGetAll(string $key): array
    {
        return static::$hashMap[$key] ?? [];
    }

    public function hMSet(string $key, array $values, int $ttl = null): bool
    {
        foreach ($values as $key => $val) {
            $bool = $this->hSet((string)$key, $val, $ttl);
            if (!$bool) {
                return false;
            }
        }
        return true;
    }

    public function hMGet(string $key, array $memberKeys): array
    {
        $map = [];
        foreach ($memberKeys as $memberKey) {
            $memberKey = strval($memberKey);
            $map[$memberKey] = static::$hashMap[$key][$memberKey] ?? null;
        }
        return $map;
    }


    public function __destruct()
    {
        self::removeRunningTrace($this->traceId);
    }

}