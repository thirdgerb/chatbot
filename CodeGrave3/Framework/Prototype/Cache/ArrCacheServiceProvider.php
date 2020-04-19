<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Cache;

use Commune\Container\ContainerContract;
use Commune\Framework\Contracts\Cache;
use Commune\Framework\Contracts\ServiceProvider;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ArrCacheServiceProvider extends ServiceProvider
{
    public function isProcessServiceProvider(): bool
    {
        return true;
    }

    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(Cache::class, ArrCache::class);
    }

    public static function stub(): array
    {
        return [];
    }


}