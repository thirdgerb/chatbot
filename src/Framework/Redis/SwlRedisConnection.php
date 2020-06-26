<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Redis;

use Commune\Blueprint\Exceptions\CommuneLogicException;
use Commune\Contracts\Redis\RedisConnection;
use Commune\Support\Swoole\SwooleUtils;
use Swoole\Database\RedisPool as SwooleRedisPool;
use Redis;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwlRedisConnection implements RedisConnection
{

    /**
     * @var SwooleRedisPool
     */
    protected $pool;

    /**
     * @var Redis
     */
    protected $client;

    /**
     * SwlRedisConnection constructor.
     * @param SwooleRedisPool $pool
     */
    public function __construct(SwooleRedisPool $pool)
    {
        $this->pool = $pool;
    }


    /**
     * @return Redis
     */
    public function get()
    {
        if (!SwooleUtils::isAtCoroutine()) {
            throw new CommuneLogicException('swoole connection must be called in the coroutine');
        }

        return $this->client
            ?? $this->client = $this->pool->get();
    }

    public function release(): void
    {
        $this->pool->put($this->client);
        unset($this->pool, $this->client);
    }




}