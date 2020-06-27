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

use Commune\Contracts\Redis\RedisConnection;
use Commune\Contracts\Redis\RedisPool;
use Commune\Support\Swoole\RedisOption;
use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool as SwooleRedisPool;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class SwlRedisPool implements RedisPool
{
    /**
     * @var RedisOption
     */
    protected $option;

    /**
     * @var SwooleRedisPool
     */
    protected $pool;

    /**
     * SwlRedisPool constructor.
     * @param RedisConfig $config
     * @param int $size
     */
    public function __construct(RedisConfig $config, int $size)
    {
        $this->pool = new SwooleRedisPool($config, $size);
    }

    public function get(): RedisConnection
    {
        return new SwlRedisConnection($this->pool);
    }


}