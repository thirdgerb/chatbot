<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Component\Providers;

use Commune\Blueprint\Exceptions\Logic\InvalidArgumentException;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\Meta\StorageOption;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Registry\Storage\FileStorageOption;

/**
 * 将组件里的 resource 文件夹下的配置先读取到注册表.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id
 * @property-read string $componentName
 * @property-read string $resourceName
 * @property-read string $resourcePath
 * @property-read string $optionClass
 * @property-read string $loader
 *
 */
class LoadComponentOption extends ServiceProvider
{

    const IDENTITY = 'id';

    public static function stub(): array
    {
        return [
            'componentName' => '',
            'resourceName' => '',
            'resourcePath' =>  __DIR__ . '/../resources',
            'optionClass' => '',
            'loader' => FileStorageOption::OPTION_PHP,
        ];
    }

    public function __get_id() : string
    {
        return static::class
            . ':'
            . $this->componentName
            . ':'
            . $this->resourceName;
    }


    public static function makeComponentOptionId(
        string $componentName,
        string $resourceName
    ) : string
    {
        return "$componentName:$resourceName:preload";
    }

    public function boot(ContainerContract $app): void
    {
        $name = static::makeComponentOptionId(
            $this->componentName,
            $this->resourceName
        );

        /**
         * @var OptRegistry $registry
         */
        $registry = $app->get(OptRegistry::class);
        $registry->registerCategory(new CategoryOption([
            'name' => $name,
            'optionClass' => $this->optionClass,
            'title' => $this->componentName . ':' . $this->resourceName,
            'desc' => 'preload option '
                . $this->optionClass
                . ' by component '
                . $this->componentName,

            'storage' => $this->getStorageOption(),

            'initialStorage' => null,
        ]));
    }

    protected function getStorageOption() : StorageOption
    {
        $abstract = FileStorageOption::OPTIONS[$this->loader];

        return new $abstract([
            'path' => $this->getPath(),
            'isDir' => true,
            'depth' => 0,
        ]);
    }

    protected function getPath() : string
    {
        $resourcePath = realpath($this->resourcePath);
        if (empty($resourcePath) || !is_dir($resourcePath)) {
            $componentName = $this->componentName;
            throw new InvalidArgumentException(
                "component $componentName resource path $resourcePath is invalid dir"
            );
        }

        $resourceName = $this->resourceName;
        $path = realpath($resourcePath . '/' . $resourceName);

        if (empty($path) || !is_dir($path)) {
            $componentName = $this->componentName;
            throw new InvalidArgumentException(
                "component $componentName resource $resourceName file path $path is invalid dir"
            );
        }

        return $path;
    }

    public function register(ContainerContract $app): void
    {
    }


}