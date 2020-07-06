<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Cache;

use Redis;
use Commune\Contracts\Cache;
use Psr\SimpleCache\CacheInterface;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @mixin Cache
 */
trait RedisCacheTrait
{

    abstract public function parseKey(string $key) : string;

    public function set(string $key, string $value, int $ttl = null): bool
    {
        $key = $this->parseKey($key);

        return $this->call(
            __METHOD__,
            function($client) use ($key, $value, $ttl){
                /** @var Redis $client */
                if (isset($ttl) && $ttl > 0) {
                    return $client->setex($key, $ttl, $value);
                }

                return $client->set($key, $value);
            }
        );
    }

    public function has(string $key): bool
    {
        $key = $this->parseKey($key);

        return $this->call(
            __METHOD__,
            function($client) use ($key) {
                /** @var Redis $client */
                return $client->exists($key);
            }
        );
    }

    public function get(string $key): ? string
    {
        $key = $this->parseKey($key);

        return $this->call(
            __METHOD__,
            function($client) use ($key) {
                /** @var Redis $client */

                $value = $client->get($key);

                if ($value === false) {
                    return null;
                }

                return $value;
            }
        );
    }


    protected function checkKey(string $method, $key) : void
    {
        if (!is_string($key)) {
            throw new Psr16InvalidArgsException($method);
        }
    }

    public function getMultiple(array $keys, array $default = null): array
    {
        foreach ($keys as $key) {
            $this->checkKey(__METHOD__, $key);
        }

        $keys = array_map([$this,'parseKey'], $keys);

        return $this->call(
            __METHOD__,
            function($client) use ($keys, $default) {
                /** @var Redis $client */

                $values = $client->mget($keys);

                if (empty($default)) {
                    return null;
                }

                foreach ($default as $key => $val) {
                    $values[$key] = $values[$key] ?? $val;
                }

                return $values;
            }
        );

    }

    public function setMultiple(array $values, int $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->checkKey(__METHOD__, $key);
            $values[$this->parseKey($key)] = $value;
        }

        return $this->call(
            __METHOD__,
            function($client) use ($values, $ttl) {
                /** @var Redis $client */

                if (empty($ttl)) {
                    return $client->mset($values);
                }

                $success = true;
                foreach ($values as $key => $val) {
                    $success = $success && $client->setex($key, $ttl, $val);
                }

                return $success;
            }
        );
    }

    public function expire(string $key, int $ttl): bool
    {
        $key = $this->parseKey($key);

        return $this->call(
            __METHOD__,
            function($client) use ($key, $ttl) {
                /** @var Redis $client */

                return $client->expire($key, $ttl);
            }
        );
    }

    public function forget(string $key): bool
    {
        $key = $this->parseKey($key);

        return $this->call(
            __METHOD__,
            function($client) use ($key) {
                /** @var Redis $client */
                return $client->del($key) > 0;
            }
        );
    }

    public function delMultiple(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->checkKey(__METHOD__, $key);
        }

        $keys = array_map([$this, 'parseKey'], $keys);

        return $this->call(
            __METHOD__,
            function($client) use ($keys) {
                /** @var Redis $client */
                return $client->del(...$keys) > 0;
            }
        );
    }

    public function lock(string $key, int $ttl = null): bool
    {
        $key = $this->parseKey($key);

        return $this->call(
            __METHOD__,
            function($client) use ($key, $ttl) {
                /** @var Redis $client */

                $locked = $client->setnx($key, 'y');
                if ($locked && isset($ttl)) {
                    $client->expire($key, $ttl);
                }
                return $locked;
            }
        );
    }

    public function unlock(string $key): bool
    {
        return $this->forget($key);
    }

    public function hSet(string $key, string $memberKey, string $value, int $ttl = null): bool
    {
        $key = $this->parseKey($key);

        return $this->call(
            __METHOD__,
            function($client) use ($key, $memberKey, $value, $ttl) {
                /** @var Redis $client */
                $result = $client->hSet(
                    $key,
                    $memberKey,
                    $value
                );

                $success = $result !== false;

                if ($success && isset($ttl)) {
                    $client->expire($key, $ttl);
                }

                return $success;
            }
        );
    }

    public function hMSet(string $key, array $values, int $ttl = null): bool
    {
        $key = $this->parseKey($key);

        foreach ($values as $valKey => $val) {
            $this->checkKey(__METHOD__, $valKey);
        }

        return $this->call(
            __METHOD__,
            function($client) use ($key, $values, $ttl) {
                /** @var Redis $client */
                $success = $client->hMset(
                    $key,
                    $values
                );

                if ($success && isset($ttl)) {
                    $client->expire($key, $ttl);
                }

                return $success;
            }
        );
    }

    public function hMGet(string $key, array $memberKeys): array
    {
        $key = $this->parseKey($key);

        foreach ($memberKeys as $memberKey) {
            $this->checkKey(__METHOD__, $memberKey);
        }

        return $this->call(
            __METHOD__,
            function($client) use ($key, $memberKeys) {
                /** @var Redis $client */
                return $client->hMGet($key, $memberKeys);
            }
        );
    }

    public function hGet(string $key, string $memberKey): ? string
    {
        $key = $this->parseKey($key);

        return $this->call(
            __METHOD__,
            function($client) use ($key, $memberKey) {
                /** @var Redis $client */
                $result = $client->hGet($key, $memberKey);
                return $result === false ? null : $result;
            }
        );
    }

    public function hGetAll(string $key): array
    {
        $key = $this->parseKey($key);

        return $this->call(
            __METHOD__,
            function($client) use ($key) {
                /** @var Redis $client */
                return $client->hGetAll($key);
            }
        );
    }

    public function getPSR16Cache(): CacheInterface
    {
        return $this->psr16
            ?? $this->psr16 = new Psr16CacheAdapter($this);
    }


    abstract protected function call(string $method, callable $query);


}