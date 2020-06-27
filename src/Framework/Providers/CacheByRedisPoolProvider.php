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

use Commune\Contracts\Cache;
use Commune\Container\ContainerContract;
use Commune\Contracts\Redis\RedisPool;
use Commune\Contracts\ServiceProvider;
use Commune\Framework\Cache\RedisPoolCache;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $cachePrefix   缓存的默认前缀.
 */
class CacheByRedisPoolProvider extends ServiceProvider
{

    public static function stub(): array
    {
        return [
            'cachePrefix' => 'cmu:',
        ];
    }


    public function getDefaultScope(): string
    {
        // 如果使用连接池, 就可以作为进程级单例.
        return self::SCOPE_PROC;
    }

    public function getId(): string
    {
        return Cache::class;
    }


    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(
            Cache::class,
            function(ContainerContract $app) {

                $pool = $app->make(RedisPool::class);
                return new RedisPoolCache($pool, $this->cachePrefix);
            }
        );
    }



}