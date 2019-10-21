<?php


namespace Commune\Components\SimpleWiki;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Components\SimpleWiki\Options\GroupOption;
use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Components\SimpleWiki\Options\WikiOption;
use Commune\Components\SimpleWiki\Options\YamlPathStorageMeta;
use Commune\Components\SimpleWiki\Providers\SimpleWikiServiceProvider;
use Commune\Support\OptionRepo\Options\CategoryMeta;
use Commune\Support\OptionRepo\Options\MetaHolder;
use Commune\Support\OptionRepo\Storage\Memory\MemoryStorageMeta;

/**
 * 用简单的 yaml 文件来生成意图.
 * 用 yaml 文件的路径作为意图的名称. 默认规则是 sfi.{group}.{path1}.{...pathN}
 * 基本逻辑是, 命中了意图后, 输出内容, 并提供猜您想问, 关联到别的意图.
 *
 * 相当于用意图做搜索的功能.
 * 是一种文档型知识库比较简洁的实现手段.
 *
 *
 * @property-read GroupOption[] $groups 分组的配置
 *
 * 相关资源:
 * @property-read string $langPath 翻译文件的路径. 为空则不加载.
 * @property-read MetaHolder $rootStorage 读取配置的根storage
 * @property-read MetaHolder[] $storagePipeline 配置的缓存storage
 */
class SimpleWikiComponent extends ComponentOption
{
    protected static $associations = [
        'groups[]' => GroupOption::class,
        'rootStorage' => MetaHolder::class,
        'storagePipeline[]' => MetaHolder::class,
    ];

    public static function stub(): array
    {
        return [

            'groups' => [

                // 系统自带的 demo
                [
                    'id' => 'demo',
                    'intentAlias' => [
                        // alias => intentName
                    ],
                    'defaultSuggestions' => [
                        // default suggestions
                        '返回上一层' => Redirector::goFulfill(),
                        '退出' => Redirector::goCancel(),
                    ],
                    'question' => 'ask.needs',
                    'askContinue' => 'ask.continue',
                    'messagePrefix' => 'demo.simpleWiki',
                ],

            ],

            'langPath' => __DIR__ .'/resources/trans',

            'rootStorage' => [
                'meta' => YamlPathStorageMeta::class,
                'config' => [
                    'path' => __DIR__ .'/resources/wiki',
                    'depth' => '>= 1', // 第一层目录会作为 group 的分组ID.
                    'isDir' => true,
                ]

            ],
            'storagePipeline' => [
                'mem' => [
                    'meta' => MemoryStorageMeta::class,
                ]
            ],

        ];
    }

    protected function doBootstrap(): void
    {
        // 加载语言配置文件.
        $langPath = $this->langPath;
        if (!empty($langPath)) {
            $this->loadTranslationResource($this->langPath);
        }

        // 加载 category meta 配置
        $data = $this->toArray();
        $this->loadOptionRepoCategoryMeta(new CategoryMeta([
            'name' => WikiOption::class,

            'optionClazz' => WikiOption::class,

            'rootStorage' => $data['rootStorage'] ?? [],

            'storagePipeline' => $data['storagePipeline'] ?? [],
        ]));

        // 注册 simple wiki registrar
        $this->app->registerProcessService(
            SimpleWikiServiceProvider::class
        );

    }

    public function getGroupByWikiOption(WikiOption $option) : GroupOption
    {
        $groupName = $option->getGroupName();
        foreach ($this->groups as $group) {
            if ($group->id === $groupName) {
                return $group;
            }
        }

        return GroupOption::createById($groupName);
    }


}