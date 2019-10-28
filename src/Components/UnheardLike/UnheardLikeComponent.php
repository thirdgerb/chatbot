<?php


namespace Commune\Components\UnheardLike;


use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Components\UnheardLike\Options\Episode;
use Commune\Components\UnheardLike\Providers\UnheardLikeServiceProvider;
use Commune\Support\OptionRepo\Options\CategoryMeta;
use Commune\Support\OptionRepo\Options\MetaHolder;
use Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta;

/**
 * @property-read string $langPath
 *
 * @property-read MetaHolder $rootStorage
 * @property-read MetaHolder[] $storagePipeline
 */
class UnheardLikeComponent extends ComponentOption
{
    protected static $associations =[
        'rootStorage' => MetaHolder::class,
        'storagePipeline[]' => MetaHolder::class,
    ];

    public static function stub(): array
    {
        return [
            'langPath' => realpath(__DIR__ . '/resources/trans'),

            'rootStorage' => [
                'meta' => YamlStorageMeta::class,
                'config' => [
                    'name' => static::class,
                    'path' => __DIR__ . '/resources/scripts',
                    'isDir' => true,
                    'depth' => 0,
                    'inline' => 6,
                    'intent' => 2,
                ],
            ],

            'storagePipeline' => [],
        ];
    }

    protected function doBootstrap(): void
    {
        // 注册上下文 .
        $this->loadSelfRegisterByPsr4(
            "Commune\\Components\\UnheardLike\\Contexts",
            __DIR__ . '/Contexts'
        );

        // 读取文本
        $this->loadTranslationResource($this->langPath);


        // 加载配置
        $data = $this->toArray();
        $this->loadOptionRepoCategoryMeta(new CategoryMeta([

            'name' => Episode::class,

            'optionClazz' => Episode::class,

            'rootStorage' => $data['rootStorage'] ?? [],
            'storagePipeline' => $data['storagePipeline'] ?? [],
        ]));


        // 注册服务
        $this->app->registerProcessService(UnheardLikeServiceProvider::class);

    }



}