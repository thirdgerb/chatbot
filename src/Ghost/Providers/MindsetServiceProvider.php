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

use Commune\Ghost\IMindset;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Blueprint\Ghost\MindMeta;
use Commune\Container\ContainerContract;
use Commune\Contracts\ServiceProvider;
use Commune\Ghost\Providers\Option\MindCacheExpireOption;
use Commune\Ghost\Providers\Option\MindStorageOption;
use Commune\Support\Registry\Meta\CategoryOption;
use Commune\Support\Registry\OptRegistry;

/**
 * Ghost Mind 的服务注册. 配置类服务.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read bool $initializeContexts  是否要在初始化的时候, 主动注册所有的 Context. 这样可以确保绝大多数 Intent 和 Stage 都得到注册.
 *
 * @property-read MindStorageOption $storage 所有注册表的存储介质.
 * @property-read MindStorageOption $initialStorage 所有注册表初始值的存储介质.
 * @property-read MindCacheExpireOption $cacheExpires 所有注册表的缓存过期时间.
 */
class MindsetServiceProvider extends ServiceProvider
{
    public static function stub(): array
    {
        return [
            'initializeContexts' => true,
            'storage' => MindStorageOption::stub(),
            'initialStorage' => MindStorageOption::stub(),
            'cacheExpires' => MindCacheExpireOption::stub(),
        ];
    }

    public static function relations(): array
    {
        return [
            'initContexts' => true,
            'storage' => MindStorageOption::class,
            'initStorage' => MindStorageOption::class,
            'cacheExpires' => MindCacheExpireOption::class,
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

        // 主动跑一次所有 Context 的初始化.
        if ($this->initializeContexts) {
            /**
             * @var Mindset $mindset
             */
            $mindset = $app->get(Mindset::class);
            $mindset->initContexts();
        }
    }

    protected function eachCategory() : \Generator
    {
        $metas = [
            'context' => MindMeta\ContextMeta::class,
            'stage' => MindMeta\StageMeta::class,
            'intent' => MindMeta\IntentMeta::class,
            'memory' => MindMeta\MemoryMeta::class,
            'emotion' => MindMeta\EmotionMeta::class,
            'entity' => MindMeta\EntityMeta::class,
            'synonym' => MindMeta\SynonymMeta::class,
        ];

        // 遍历获取所有的 Category 配置.
        foreach ($metas as $type => $metaName) {
            yield new CategoryOption([
                'name' => $metaName,
                'optionClass' => $metaName,
                'title' => $metaName,
                'desc' => '',
                'cacheExpire' => $this->cacheExpires->{$type} - 5,
                'storage' => $this->storage->{$type},
                'initialStorage' => $this->initialStorage->{$type},
            ]);
        }
    }

    public function register(ContainerContract $app): void
    {
        $app->singleton(Mindset::class, IMindset::class);
    }


}