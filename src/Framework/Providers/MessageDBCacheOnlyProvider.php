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
use Commune\Contracts\Cache;
use Commune\Contracts\Messenger\MessageDB;
use Commune\Contracts\ServiceProvider;
use Commune\Framework\Messenger\MessageDB\CacheOnlyMessageDB;
use Psr\Log\LoggerInterface;


/**
 * 基于 Cache 提供 MessageDB 的服务, 但仅仅用于缓存.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read int $cacheTtl 缓存过期时间.
 */
class MessageDBCacheOnlyProvider extends ServiceProvider
{
    /**
     * 请求级服务.
     * @return string
     */
    public function getDefaultScope(): string
    {
        return self::SCOPE_REQ;
    }

    public static function stub(): array
    {
        return [
            'cacheTtl' => 10,
        ];
    }

    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(
            MessageDB::class,
            function(ContainerContract $app) {

                $cache = $app->make(Cache::class);
                $logger = $app->make(LoggerInterface::class);


                return new CacheOnlyMessageDB(
                    $cache,
                    $logger,
                    $this->cacheTtl
                );
            }
        );
    }


}