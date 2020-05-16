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
use Commune\Contracts\ServiceProvider;
use Commune\Framework\Cache\ArrayCache;


/**
 * 用数组模拟缓存的模块. 也可以用于单体机器人中. 但不存在过期功能.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ArrCacheServiceProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [];
    }

    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(
            Cache::class,
            ArrayCache::class
        );
    }


}