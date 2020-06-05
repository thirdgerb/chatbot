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
 * @property-read string $resourcePath
 * @property-read int $cacheExpire
 *
 */
class MindsetStorageConfigProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [
            'resourcePath' => realpath(__DIR__ .'/../../../demo/resources'),
            'cacheExpire' => 600,
        ];
    }

    public function getDefaultScope(): string
    {
        return self::SCOPE_CONFIG;
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
            'contexts' => [MindMeta\ContextMeta::class, PHPStorageOption::class, '语境', '上下文语境配置'],
            'stages' => [MindMeta\StageMeta::class, PHPStorageOption::class, 'stage', '多轮对话节点逻辑配置'],
            'intents' => [MindMeta\IntentMeta::class, YmlStorageOption::class, '意图', '对话意图配置'],
            'memories' => [MindMeta\MemoryMeta::class, YmlStorageOption::class, '记忆', '上下文记忆配置'],
            'emotions' => [MindMeta\EmotionMeta::class, YmlStorageOption::class, '情绪', '情绪逻辑配置, 用于复杂意图匹配'],
            'entities' => [MindMeta\EntityMeta::class, YmlStorageOption::class, '实体词典', '实体词典配置'],
            'synonyms' => [MindMeta\SynonymMeta::class, YmlStorageOption::class, '同义词词典', '同义词词典配置'],
        ];
    }

    protected function eachCategory() : \Generator
    {
        $metas = $this->getCategoriesConfigs();

        // 遍历获取所有的 Category 配置.
        foreach ($metas as $type => list($metaName, $storageName, $title, $desc)) {
            /**
             * @var StorageOption $storageOption
             */
            $storageOption = new $storageName([
                'name' => $type,
                'path' => $this->resourcePath . '/' . $type,
                'isDir' => true,
            ]);

            $meta = $storageOption->toMeta();

            yield new CategoryOption([
                'name' => $metaName,
                'optionClass' => $metaName,
                'title' => $title,
                'desc' => $desc,
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