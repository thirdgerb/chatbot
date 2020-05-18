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
use Commune\Contracts\ServiceProvider;
use Commune\Support\Registry\Impl\IOptRegistry;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Registry\Storage\Json\JsonFileStorage;
use Commune\Support\Registry\Storage\PHP\PHPFileStorage;
use Commune\Support\Registry\Storage\Yaml\YmlFileStorage;
use Psr\Log\LoggerInterface;

/**
 * 配置注册表模块. 能够从指定的存储介质中获得配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read CategoryOption[] $categories 系统默认的配置.
 */
class OptRegistryProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [
            'categories' => [],
        ];
    }

    public static function relations(): array
    {
        return [
            'categories[]' => CategoryOption::class
        ];
    }

    public function boot(ContainerContract $app): void
    {
        $registry = $app->get(OptRegistry::class);

        foreach ($this->categories as $categoryOption) {
            $registry->registerCategory($categoryOption);
        }
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(OptRegistry::class, function(ContainerContract $app){

            return new IOptRegistry(
                $app,
                $app->get(LoggerInterface::class)
            );

        });

        $app->singleton(JsonFileStorage::class);
        $app->singleton(PHPFileStorage::class);
        $app->singleton(YmlFileStorage::class);
    }


}