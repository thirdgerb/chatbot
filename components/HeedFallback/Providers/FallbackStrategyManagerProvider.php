<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\HeedFallback\Providers;

use Commune\Components\HeedFallback\Data\FallbackStrategyInfo;
use Commune\Components\HeedFallback\Libs\FallbackSceneRepository;
use Commune\Components\HeedFallback\Libs\FallbackStrategyManager;
use Commune\Components\HeedFallback\Libs\IFallbackStrategyManager;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read FallbackStrategyInfo[] $strategies
 * @property-read string $repositoryConcrete
 */
class FallbackStrategyManagerProvider extends ServiceProvider
{
    public function getDefaultScope(): string
    {
        return self::SCOPE_PROC;
    }

    public static function stub(): array
    {
        return [
            'strategies' => [],
            'repositoryConcrete' => ''
        ];
    }

    public static function relations(): array
    {
        return [
            'strategies[]' => FallbackStrategyInfo::class
        ];
    }

    public function boot(ContainerContract $app): void
    {
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(
            FallbackStrategyManager::class,
            IFallbackStrategyManager::class
        );

        $app->singleton(
            FallbackSceneRepository::class,
            $this->repositoryConcrete
        );
    }


}