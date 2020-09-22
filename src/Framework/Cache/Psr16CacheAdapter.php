<?php


namespace Commune\Framework\Cache;


use Commune\Contracts\Cache;
use Commune\Framework\Spy\SpyAgency;
use Psr\SimpleCache\CacheInterface;

class Psr16CacheAdapter implements CacheInterface
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * ArrayPsr16Cache constructor.
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
        SpyAgency::incr(static::class);
    }

    protected function checkKey(string $method, $key) : void
    {
        if (!is_string($key)) {
            throw new Psr16InvalidArgsException($method);
        }
    }


    public function get($key, $default = null)
    {
        $this->checkKey(static::class . '::'. __FUNCTION__, $key);
        $value = $this->cache->get($key);
        $value = isset($value) ? unserialize($value) : null;
        return $value ?? $default;
    }

    public function set($key, $value, $ttl = null)
    {
        $this->checkKey(static::class . '::'. __FUNCTION__, $key);
        $value = serialize($value);
        $this->cache->set($key, $value, intval($ttl));
    }

    public function delete($key)
    {
        $this->checkKey(static::class . '::'. __FUNCTION__, $key);
        $this->cache->forget($key);
    }

    public function clear()
    {
        return false;
    }

    public function getMultiple($keys, $default = null)
    {
        foreach ($keys as $key) {
            $this->checkKey(static::class . '::'. __FUNCTION__, $key);
        }
        $values = $this->cache->getMultiple($keys);

        return array_map(function($value) use ($default){
            return is_string($value) ? unserialize($value) : $default;
        }, $values);
    }

    public function setMultiple($values, $ttl = null)
    {
        $inputs = [];
        foreach ($values as $key => $value) {
            $this->checkKey(static::class . '::'. __FUNCTION__, $key);
            $inputs[$key] = serialize($value);
        }
        return $this->cache->setMultiple($inputs, $ttl);
    }

    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->checkKey(static::class . '::'. __FUNCTION__, $key);
        }
        $this->cache->delMultiple($keys);
    }

    public function has($key)
    {
        return $this->cache->has($key);
    }


    public function __destruct()
    {
        unset($this->cache);
        SpyAgency::decr(static::class);
    }
}