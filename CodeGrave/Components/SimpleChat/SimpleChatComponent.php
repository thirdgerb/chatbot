<?php


namespace Commune\Components\SimpleChat;

use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Components\SimpleChat\Options\ChatOption;
use Commune\Components\SimpleChat\Providers\RegisterSimpleChat;
use Commune\Support\OptionRepo\Options\CategoryMeta;
use Commune\Support\OptionRepo\Options\MetaHolder;
use Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta;


/**
 * 闲聊组件. 用最简单的方式来定义闲聊.
 * 在目录下定义多个文件, 每个文件里定义一套闲聊策略.
 *
 * 可以在 hearing 中 使用 SimpleChatAction, 来开启闲聊.
 *
 * 例如:
 * $hearing->interceptor(new SimpleChatAction($domain),
 * $hearing->fallback(new SimpleChatAction($domain),
 *
 * @property-read MetaHolder $rootStorage 读取配置的 根 storage
 * @property-read MetaHolder[] $storagePipeline 读取配置的缓存层.
 *
 */
class SimpleChatComponent extends ComponentOption
{

    protected static $associations = [
        'rootStorage' => MetaHolder::class,
        'storagePipeline[]' => MetaHolder::class,
    ];


    public static function stub(): array
    {
        return [
            'rootStorage' => [
                'meta' => YamlStorageMeta::class,
                'config' => [
                    'path' => __DIR__ . '/resources/example.yml',
                    'isDir' => false,
                ],
            ],
            'storagePipeline' => [
            ]
        ];
    }

    protected function doBootstrap(): void
    {
        $data = $this->toArray();

        // 注册 category meta
        $this->loadOptionRepoCategoryMeta(new CategoryMeta([
            'name' => ChatOption::class,
            'optionClazz' => ChatOption::class,
            'rootStorage' => $data['rootStorage'] ?? [],
            'storagePipeline' => $data['storagePipeline'] ?? [],
        ]));


        // 为并不存在的 intent 注册 place holder
        $this->app->registerProcessService(RegisterSimpleChat::class);

    }



}