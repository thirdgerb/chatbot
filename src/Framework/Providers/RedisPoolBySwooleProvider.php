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
use Swoole\Database\RedisConfig;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $host
 * @property-read int $port
 * @property-read string $auth
 * @property-read int $dbIndex
 * @property-read float $timeout
 * @property-read float $readTimeout
 * @property-read int $retryInterval
 * @property-read string $reserved
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
            'host' => '127.0.0.1',
            'port' => 6379,
            'auth' => '',
            'dbIndex' => 0,
            'timeout' => 0.0,
            'retryInterval' => 0,
            'readTimeout' => 0.0,
            'reserved' => '',
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

                $config = new RedisConfig();
                $config = $config
                    ->withHost($this->host)
                    ->withPort($this->port)
                    ->withAuth($this->auth)
                    ->withDbIndex($this->dbIndex)
                    ->withTimeout($this->timeout)
                    ->withRetryInterval($this->retryInterval)
                    ->withReserved($this->reserved);

                return new SwlRedisPool($config);
            }
        );
    }


}