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

use Commune\Blueprint\CommuneEnv;
use Commune\Blueprint\Ghost\MindMeta;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;
use Commune\Ghost\IMindset;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\Meta\StorageMeta;
use Commune\Support\Registry\Meta\StorageOption;
use Commune\Support\Registry\OptRegistry;
use Commune\Support\Registry\Storage\Json\JsonStorageOption;
use Commune\Support\Registry\Storage\PHP\PHPStorageOption;
use Commune\Support\Registry\Storage\Yaml\YmlStorageOption;

/**
 * Ghost Mind 的服务注册. 配置类服务.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read int $mindsetCacheExpire       Mindset 里用内存缓存 definition 的过期时间.
 *
 *
 * @property-read StorageMeta|null $storage     自定义的存储介质.
 *
 * @property-read int $storageCacheExpire       默认介质的缓存过期时间.
 * @property-read string $resourcePath          默认的资源库路径.
 *
 * @property-read bool $useFileInitStorage      是否使用文件 storage 作为默认.
 */
class MindsetStorageConfigProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [
            'mindsetCacheExpire' => 599,
            'resourcePath' => CommuneEnv::getResourcePath(),
            'storageCacheExpire' => 600,
            'storage' => null,
            'useFileInitStorage' => true,
        ];
    }

    public function getDefaultScope(): string
    {
        return self::SCOPE_CONFIG;
    }

    public static function relations(): array
    {
        return [
            'storage' => StorageMeta::class,
        ];
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
            // 逻辑库
            'contexts' => [
                MindMeta\ContextMeta::class,
                PHPStorageOption::class,
                '语境',
                '上下文语境配置',
                null
            ],
            'stages' => [
                MindMeta\StageMeta::class,
                PHPStorageOption::class,
                'stage',
                '多轮对话节点逻辑配置',
                null,
            ],
            'intents' => [
                MindMeta\IntentMeta::class,
                PHPStorageOption::class,
                '意图',
                '对话意图配置',
                null,
            ],
            'memories' => [
                MindMeta\MemoryMeta::class,
                PHPStorageOption::class,
                '记忆',
                '上下文记忆配置',
                null
            ],
            'emotions' => [
                MindMeta\EmotionMeta::class,
                PHPStorageOption::class,
                '情绪',
                '情绪逻辑配置, 用于复杂意图匹配',
                null,
            ],

            // 语料库
            'entities' => [
                MindMeta\EntityMeta::class,
                YmlStorageOption::class,
                '实体词典',
                '实体词典配置',
                null,
            ],
            'synonyms' => [
                MindMeta\SynonymMeta::class,
                YmlStorageOption::class,
                '同义词词典',
                '同义词词典配置',
                '/data.yml',
            ],
            'chats' => [
                MindMeta\ChatMeta::class,
                JsonStorageOption::class,
                '闲聊语料库',
                '闲聊语料库配置',
                '/data.json'
            ]
        ];
    }

    protected function eachCategory() : \Generator
    {
        $metas = $this->getCategoriesConfigs();

        // 遍历获取所有的 Category 配置.
        foreach ($metas as $name => list($metaName, $storageName, $title, $desc, $path)) {
            $path = $path ?? '';
            $resourcePath = $this->resourcePath . '/' . $name . $path;
            /**
             * @var StorageOption $storage
             */
            $initStorage = $this->useFileInitStorage
                ? new StorageMeta([
                    'wrapper' => $storageName,
                    'config' => [
                        'name' => $name,
                        'path' => $resourcePath,
                        'isDir' => is_dir($resourcePath),
                    ]
                ])
                : null;

            $definedStorage = $this->storage;


            yield new CategoryOption([
                'name' => $metaName,
                'optionClass' => $metaName,
                'title' => $title,
                'desc' => $desc,
                'cacheExpire' => $this->storageCacheExpire,
                'storage' => $definedStorage,
                'initialStorage' => $initStorage,
            ]);
        }
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(Mindset::class, function(ContainerContract $app){
            $optRegistry = $app->get(OptRegistry::class);
            return new IMindset($optRegistry, $this->mindsetCacheExpire);
        });
    }


}