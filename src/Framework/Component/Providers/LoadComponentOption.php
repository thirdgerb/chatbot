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

use Commune\Blueprint\Exceptions\CommuneBootingException;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\Meta\StorageOption;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Registry\Storage\FileStorageOption;
use Commune\Support\Utils\TypeUtils;

/**
 * 将组件里的自定义的配置, 加载到注册表 optRegistry.
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
            'optionClass' => '',
            'resourcePath' =>  '',
            // if isDir === false, then resource path should be valid file path
            'isDir' => true,
            'loader' => FileStorageOption::OPTION_PHP,
        ];
    }

    public function getDefaultScope(): string
    {
        return self::SCOPE_CONFIG;
    }

    public static function validate(array $data): ? string /* errorMsg */
    {

        return TypeUtils::requireFields($data, ['componentName', 'optionClass'])
            ?? parent::validate($data);
    }

    public function __get_id() : string
    {
        return 'load:'
            . static::class
            . ':com:'
            . $this->componentName
            . ':opt:'
            . $this->optionClass;
    }

    /**
     * 生成本组件配置在 OptRegistry 内部的一个唯一 ID
     * @param string $componentName
     * @param string $optionClass
     * @return string
     */
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

        try {

            /**
             * @var OptRegistry $registry
             */
            $registry = $app->get(OptRegistry::class);

        } catch (\Throwable $e) {
            throw new CommuneBootingException(
                OptRegistry::class . ' not registered',
                $e
            );
        }

        $registry->registerCategory(new CategoryOption([
            'name' => $name,
            'optionClass' => $this->optionClass,
            'title' => $this->componentName . ':' . $this->optionClass,
            'desc' => 'preload option '
                . $this->optionClass
                . ' by component '
                . $this->componentName,

            'storage' => null,
            'initialStorage' => $this->getStorageOption()->toMeta(),
            'temporary' => true,
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