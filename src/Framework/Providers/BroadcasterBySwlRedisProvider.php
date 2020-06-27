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
use Commune\Contracts\Messenger\Broadcaster;
use Commune\Contracts\ServiceProvider;
use Commune\Framework\Messenger\Broadcaster\SwlRedisBroadcaster;
use Commune\Support\Swoole\RedisOption;
use Psr\Log\LoggerInterface;
use Swoole\Database\RedisPool as SwooleRedisPool;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string[] $listeningShells
 * @property-read int $publishPoolSize
 * @property-read int $subscribePoolSize
 * @property-read RedisOption $option
 */
class BroadcasterBySwlRedisProvider extends ServiceProvider
{
    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
    }

    public static function stub(): array
    {
        return [
            'listeningShells' => [],
            'publishPoolSize' => SwooleRedisPool::DEFAULT_SIZE,
            'subscribePoolSize' => 2,
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
            Broadcaster::class,
            function(ContainerContract $app) {

                $logger = $app->make(LoggerInterface::class);
                return new SwlRedisBroadcaster(
                    $this->option,
                    $logger,
                    $this->listeningShells,
                    $this->publishPoolSize,
                    $this->subscribePoolSize
                );
            }
        );
    }

    public static function relations(): array
    {
        return [
            'option' => RedisOption::class,
        ];
    }

}