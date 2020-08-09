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

use Commune\Blueprint\CommuneEnv;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;
use Commune\Framework\FileCache\FileCacheOption;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\Meta\StorageMeta;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Registry\Storage\Json\JsonStorageOption;
use Commune\Support\Utils\StringUtils;


/**
 * 测试使用的模块, 用文件来存储一些数据.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $path
 */
class FileCacheServiceProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [
            'path' => StringUtils::gluePath(
                CommuneEnv::getRuntimePath(),
                'filecaches'
            )
        ];
    }

    public function getDefaultScope(): string
    {
        return self::SCOPE_CONFIG;
    }


    public function boot(ContainerContract $app): void
    {
        /**
         * @var OptRegistry $registry
         */
        $registry = $app->get(OptRegistry::class);

        $registry->registerCategory(new CategoryOption([
            'name' => FileCacheOption::class,
            'optionClass' => FileCacheOption::class,
            'title' => '测试文件缓存',
            'desc' => '',
            'storage' => new StorageMeta([
                'wrapper' => JsonStorageOption::class,
                'config' => [
                    'path' => $this->path,
                    'isDir' => true,
                    'depth' => 0,
                ],
            ])
        ]));
    }

    public function register(ContainerContract $app): void
    {
    }


}