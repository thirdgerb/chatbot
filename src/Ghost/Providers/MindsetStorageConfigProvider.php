<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Providers;

use Commune\Blueprint\Ghost\MindMeta;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;
use Commune\Support\Option\Option;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\Meta\StorageOption;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Registry\Storage\PHP\PHPStorageOption;
use Commune\Support\Registry\Storage\Yaml\YmlStorageOption;

/**
 * Ghost Mind 的服务注册. 配置类服务.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $mainDir
 *
 * @property-read int $cacheExpire
 *
 */
class MindsetStorageConfigProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [
            'mainDir' => realpath(__DIR__ .'/../../../demo/resources'),
            'cacheExpire' => 600,
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function boot(ContainerContract $app): void
    {
        /**
         * @var OptRegistry $registry
         */
        $registry = $app->get(OptRegistry::class);

        // 注册所有的配置存储配置.
        foreach ($this->eachCategory() as $option) {
            /**
             * @var CategoryOption $option
             */
            $registry->registerCategory($option);
        }
    }

    protected function getCategoriesConfigs() : array
    {
        return [
            'contexts' => [MindMeta\ContextMeta::class, PHPStorageOption::class],
            'stages' => [MindMeta\StageMeta::class, PHPStorageOption::class],
            'intents' => [MindMeta\IntentMeta::class, YmlStorageOption::class],
            'memories' => [MindMeta\MemoryMeta::class, YmlStorageOption::class],
            'emotions' => [MindMeta\EmotionMeta::class, YmlStorageOption::class],
            'entities' => [MindMeta\EntityMeta::class, YmlStorageOption::class],
            'synonyms' => [MindMeta\SynonymMeta::class, YmlStorageOption::class],
        ];
    }

    protected function eachCategory() : \Generator
    {
        $metas = $this->getCategoriesConfigs();

        // 遍历获取所有的 Category 配置.
        foreach ($metas as $type => list($metaName, $storageName)) {
            /**
             * @var StorageOption $storageOption
             */
            $storageOption = new $storageName([
                'name' => $type,
                'path' => $this->mainDir . '/' . $type,
                'isDir' => true,
            ]);

            $meta = $storageOption->getMeta();

            yield new CategoryOption([
                'name' => $metaName,
                'optionClass' => $metaName,
                'title' => call_user_func([$metaName, Option::TITLE_FUNC]),
                'desc' => call_user_func([$metaName, Option::DESC_FUNC]),
                'cacheExpire' => $this->cacheExpire,
                'storage' => $meta,
                'initialStorage' => null,
            ]);
        }
    }

    public function register(ContainerContract $app): void
    {
    }


}