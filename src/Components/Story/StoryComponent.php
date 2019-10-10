<?php


namespace Commune\Components\Story;


use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Components\Story\Options\ScriptOption;
use Commune\Components\Story\Providers\StoryServiceProvider;
use Commune\Support\OptionRepo\Options\CategoryMeta;
use Commune\Support\OptionRepo\Options\MetaHolder;
use Commune\Support\OptionRepo\Storage\Memory\MemoryStorageMeta;
use Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta;

/**
 *
 * StoryComponent 是一个情景互动游戏的示范.
 * 可参考本模块开发类似的互动游戏.
 * 也可以封装出基于配置的引擎.
 *
 * @property-read string $translationPath 脚本内容文件所在目录.
 * @property-read MetaHolder $rootStorage 配置文件根仓库的元配置.
 * @property-read MetaHolder[] $storagePipeline 配置文件缓存仓库的元配置.
 *
 */
class StoryComponent extends ComponentOption
{

    protected static $associations = [
        'rootStorage' => MetaHolder::class,
        'storagePipeline[]' => MetaHolder::class,
    ];

    public static function stub(): array
    {
        return [
            'translationPath' => __DIR__ . '/langs',
            'rootStorage' => [
                'meta' => YamlStorageMeta::class,
                'config' => [
                    'name' => 'yaml',
                    'path' => __DIR__ . '/examples/',
                    'isDir' => true,
                ],
            ],
            'storagePipeline' => [
                'mem' => [
                    'meta' => MemoryStorageMeta::class,
                    'config' => [
                        'name' => 'mem',
                    ],
                ],
            ],
        ];
    }


    protected function doBootstrap(): void
    {
        // 默认加载脚本几个类.
        $this->loadSelfRegisterByPsr4(
            "Commune\\Components\\Story\\Intents\\",
            __DIR__ . '/Intents/'
        );


        // 注册所有的脚本配置.
        $data = $this->toArray();
        $this->loadOptionRepoCategoryMeta(
            new CategoryMeta([
                'name' => ScriptOption::class,
                'optionClazz' => ScriptOption::class,
                'rootStorage' => $data['rootStorage'],
                'storagePipeline' => $data['storagePipeline'],
            ])
        );

        // 注册文本文件.
        $this->loadTranslationResource($this->translationPath);

        // 最后注册 Story 容器.
        $this->app->registerProcessService(
            new StoryServiceProvider(
                $this->app->getProcessContainer(),
                $this
            )
        );
    }

}