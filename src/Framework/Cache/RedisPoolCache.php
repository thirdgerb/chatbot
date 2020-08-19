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

use Commune\Contracts\Cache;
use Commune\Contracts\Redis\RedisPool;
use Commune\Framework\Spy\SpyAgency;
use Commune\Blueprint\Exceptions\IO\DataIOException;
use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Blueprint\Exceptions\CommuneRuntimeException;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class RedisPoolCache implements Cache
{
    use RedisCacheTrait;

    /**
     * @var RedisPool
     */
    protected $pool;

    /**
     * @var Psr16CacheAdapter
     */
    protected $psr16;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * RedisPoolCache constructor.
     * @param RedisPool $pool
     * @param string $prefix
     */
    public function __construct(RedisPool $pool, string $prefix = '')
    {
        $this->pool = $pool;
        $this->prefix = $prefix;
        SpyAgency::incr(static::class);
    }

    public function parseKey(string $key) : string
    {
        return $this->prefix . $key;
    }


    protected function call(string $method, callable $query)
    {
        $connection = $this->pool->get();
        try {

            $redis = $connection->get();
            $result = $query($redis);
            $connection->release();
            unset($connection, $redis);
            return $result;

        } catch (CommuneRuntimeException $e) {
            throw $e;

        } catch (CommuneLogicException $e) {
            throw $e;

        } catch (\Throwable $e) {
            throw new DataIOException($method . ' failed', $e);
        }
    }

    public function __destruct()
    {
        SpyAgency::decr(static::class);
    }
}