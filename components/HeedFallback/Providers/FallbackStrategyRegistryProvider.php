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

use Commune\Blueprint\CommuneEnv;
use Commune\Components\HeedFallback\Data\StrategyMatcherOption;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\Meta\StorageMeta;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Registry\Storage\Json\JsonStorageOption;
use Commune\Support\Utils\StringUtils;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $title
 * @property-read string $desc
 * @property-read StorageMeta|null $storage
 * @property-read StorageMeta $initStorage
 */
class FallbackStrategyRegistryProvider extends ServiceProvider
{
    public function getDefaultScope(): string
    {
        return self::SCOPE_CONFIG;
    }

    public static function stub(): array
    {
        return [
            'title' => '上下文回复策略',
            'desc' => '上下文相关回复策略仓库',
            'storage' => null,
            'initStorage' => self::defaultStorage()
        ];
    }

    public static function defaultStorage() : array
    {
        return [
            'wrapper' => JsonStorageOption::class,
            'config' => [
                'path' => StringUtils::gluePath(
                    CommuneEnv::getRuntimePath(),
                    'strategy/all.json'
                ),
                'isDir' => false,
            ]
        ];
    }

    public static function relations(): array
    {
        return [
            'storage' => StorageMeta::class,
            'initStorage' => StorageMeta::class,
        ];
    }

    public function boot(ContainerContract $app): void
    {
        /**
         * @var OptRegistry $registry
         */
        $registry = $app->make(OptRegistry::class);
        $registry->registerCategory(new CategoryOption([
            'name' => StrategyMatcherOption::class,
            'optionClass' => StrategyMatcherOption::class,
            'title' => $this->title,
            'desc' => $this->desc,
            'storage' => $this->storage,
            'initialStorage' => $this->initStorage,
        ]));
    }

    public function register(ContainerContract $app): void
    {
    }


}