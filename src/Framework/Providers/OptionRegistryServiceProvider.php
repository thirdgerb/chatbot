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
use Commune\Contracts\Log\ConsoleLogger;
use Commune\Contracts\ServiceProvider;
use Commune\Support\Registry\Impl\IOptRegistry;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Registry\Storage\Json\JsonFileStorageDriver;
use Commune\Support\Registry\Storage\PHP\PHPFileStorageDriver;
use Commune\Support\Registry\Storage\Yaml\YmlFileStorageDriver;

/**
 * 配置注册表模块. 能够从指定的存储介质中获得配置.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read CategoryOption[] $categories 系统默认的配置.
 */
class OptionRegistryServiceProvider extends ServiceProvider
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

    public function getDefaultScope(): string
    {
        return self::SCOPE_CONFIG;
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
            return new IOptRegistry($app);
        });

        $app->singleton(
            JsonFileStorageDriver::class,
            function(ContainerContract $app) {
                return new JsonFileStorageDriver($app->get(ConsoleLogger::class));
            }
        );

        $app->singleton(
            PHPFileStorageDriver::class,
            function(ContainerContract $app) {
                return new PHPFileStorageDriver($app->get(ConsoleLogger::class));
            }
        );

        $app->singleton(
            YmlFileStorageDriver::class,
            function(ContainerContract $app) {
                return new YmlFileStorageDriver($app->get(ConsoleLogger::class));
            }
        );
    }


}