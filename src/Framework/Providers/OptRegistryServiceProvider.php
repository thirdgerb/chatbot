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
use Commune\Framework\Contracts\ServiceProvider;
use Commune\Support\Registry\Impl\IOptRegistry;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\OptRegistry;
use Psr\Log\LoggerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read CategoryOption[] $categories 系统默认的配置.
 */
class OptRegistryServiceProvider extends ServiceProvider
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
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(OptRegistry::class, function(ContainerContract $app){

            $registry = new IOptRegistry(
                $app,
                $app->get(LoggerInterface::class)
            );

            foreach ($this->categories as $categoryOption) {
                $registry->registerCategory($categoryOption);
            }
        });
    }


}