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
 * @property-read string $resourcePath
 * @property-read string $optionClass
 * @property-read bool $isDir
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
            'resourcePath' =>  '',
            'optionClass' => '',
            'isDir' => true,
            'loader' => FileStorageOption::OPTION_PHP,
        ];
    }

    public function getDefaultScope(): string
    {
        return self::SCOPE_CONFIG;
    }

    public function __get_id() : string
    {
        return static::class
            . ':'
            . $this->componentName
            . ':'
            . $this->optionClass;
    }

    public static function makeComponentOptionId(
        string $componentName,
        string $optionClass
    ) : string
    {
        return "$componentName:$optionClass:preload";
    }

    public function boot(ContainerContract $app): void
    {
        $name = static::makeComponentOptionId(
            $this->componentName,
            $this->optionClass
        );

        /**
         * @var OptRegistry $registry
         */
        $registry = $app->get(OptRegistry::class);

        $registry->registerCategory(new CategoryOption([
            'name' => $name,
            'optionClass' => $this->optionClass,
            'title' => $this->componentName . ':' . $this->optionClass,
            'desc' => 'preload option '
                . $this->optionClass
                . ' by component '
                . $this->componentName,

            'storage' => $this->getStorageOption()->toMeta(),

            'initialStorage' => null,
        ]));
    }

    protected function getStorageOption() : StorageOption
    {
        $abstract = FileStorageOption::OPTIONS[$this->loader];

        return new $abstract([
            'path' => $this->resourcePath,
            'isDir' => $this->isDir,
            'depth' => 0,
        ]);
    }

    public function register(ContainerContract $app): void
    {
    }


}