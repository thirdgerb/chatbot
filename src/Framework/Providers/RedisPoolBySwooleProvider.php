<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Providers;

use Commune\Container\ContainerContract;
use Commune\Contracts\Redis\RedisPool;
use Commune\Contracts\ServiceProvider;
use Commune\Framework\Redis\SwlRedisPool;
use Commune\Support\Swoole\RedisOption;
use Swoole\ConnectionPool;
use Swoole\Database\RedisConfig;


/**
 * 提供 Swoole Redis Pool 服务.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read int $size
 * @property-read RedisOption $option
 */
class RedisPoolBySwooleProvider extends ServiceProvider
{
    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
    }

    public static function stub(): array
    {
        return [
            'size' => ConnectionPool::DEFAULT_SIZE,
            'option' => [
                'host' => '127.0.0.1',
                'port' => 6379,
                'auth' => '',
                'dbIndex' => 0,
                'timeout' => 1,
                'retryInterval' => 0,
                'readTimeout' => 0.0,
                'reserved' => '',
            ],
        ];
    }

    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(
            RedisPool::class,
            function(ContainerContract $app) {

                $option = $this->option;
                $config = new RedisConfig();
                $config = $config
                    ->withHost($option->host)
                    ->withPort($option->port)
                    ->withAuth($option->auth)
                    ->withDbIndex($option->dbIndex)
                    ->withTimeout($option->timeout)
                    ->withReadTimeout($option->readTimeout)
                    ->withRetryInterval($option->retryInterval)
                    ->withReserved($option->reserved);


                return new SwlRedisPool($config, $this->size);
            }
        );
    }

    public static function relations(): array
    {
        return [
            'option' => RedisOption::class  
        ];
    }

}