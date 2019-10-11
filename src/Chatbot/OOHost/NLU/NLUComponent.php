<?php


namespace Commune\Chatbot\OOHost\NLU;


use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Chatbot\OOHost\NLU\Contracts\NLULogger;
use Commune\Chatbot\OOHost\NLU\Contracts\NLUService;
use Commune\Chatbot\OOHost\NLU\Options\EntityDictOption;
use Commune\Chatbot\OOHost\NLU\Options\IntentCorpusOption;
use Commune\Chatbot\OOHost\NLU\Predefined\FakeNLUService;
use Commune\Chatbot\OOHost\NLU\Predefined\SimpleNLULogger;
use Commune\Chatbot\OOHost\NLU\Providers\CorpusRepoServiceProvider;
use Commune\Chatbot\OOHost\NLU\Providers\NLUServiceProvider;
use Commune\Support\OptionRepo\Options\CategoryMeta;
use Commune\Support\OptionRepo\Options\MetaHolder;
use Commune\Support\OptionRepo\Storage\Memory\MemoryStorageMeta;
use Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta;

/**
 * NLU 组件负责管理 commune chatbot 的 nlu 基本功能.
 *
 * 分为以下几类功能.
 * 1. 调用 nlu 的服务, 匹配用户的意图.
 * 2. 管理 nlu 的语料库, 包括意图语料库与实体词典.
 * 3. 将本地 corpus 的内容, 同步到 nlu service 上面.
 * 4. 提供一些管理员才有权限的多轮对话, 用于管理以上功能.
 *
 * @property-read string $nluService NLU 中间件的实现.
 * @property-read string $nluLogger 记录 nlu 匹配结果的日志服务.
 * @property-read MetaHolder $intentRootStorage 意图语料库的数据源配置.
 * @property-read MetaHolder[] $intentStoragePipeline 意图语料库的缓存层.
 * @property-read MetaHolder $entityRootStorage 实体词典的数据源
 * @property-read MetaHolder[] $entityStoragePipeline 实体词典的缓存层.
 */
class NLUComponent extends ComponentOption
{
    protected static $associations =[
        'intentRootStorage' => MetaHolder::class,
        'intentStoragePipeline[]' => MetaHolder::class,
        'entityRootStorage' => MetaHolder::class,
        'entityStoragePipeline[]' => MetaHolder::class,

    ];

    public static function stub(): array
    {
        return [
            'nluService' => FakeNLUService::class,

            'nluLogger' => SimpleNLULogger::class,

            'intentRootStorage' => [
                'meta' => YamlStorageMeta::class,
                'config' => [
                    'path' => __DIR__ . '/resources/nlu/intents.yaml',
                    'isDir' => false,
                ],
            ],

            'intentStoragePipeline' => [
                'mem' => [
                    'meta' => MemoryStorageMeta::class,
                ]
            ],
            'entityRootStorage' => [
                'meta' => YamlStorageMeta::class,
                'config' => [
                    'path' => __DIR__ . '/resources/nlu/entities.yaml',
                    'isDir' => false,
                ],
            ],
            'entityStoragePipeline' => [
                'mem' => [
                    'meta' => MemoryStorageMeta::class,
                ]
            ],

        ];
    }

    protected function doBootstrap(): void
    {
        // 注册仓库配置.
        $data = $this->toArray();

        $this->loadOptionRepoCategoryMeta(new CategoryMeta([
            'name' => IntentCorpusOption::class,
            'optionClazz' => IntentCorpusOption::class,
            'rootStorage' => $data['intentRootStorage'],
            'storagePipeline' => $data['intentStoragePipeline'],
        ]));

        $this->loadOptionRepoCategoryMeta(new CategoryMeta([
            'name' => EntityDictOption::class,
            'optionClazz' => EntityDictOption::class,
            'rootStorage' => $data['entityRootStorage'],
            'storagePipeline' => $data['entityStoragePipeline'],
        ]));

        // 注册请求级服务
        $this->app->registerConversationService(
            new NLUServiceProvider(
                $this->app->getConversationContainer(),
                $this
            )
        );

    }

    public static function validate(array $data): ? string
    {
        if (!is_a($data['nluService'] ?? '', NLUService::class, TRUE)) {
            return 'nlu service is invalid, should implements '.NLUService::class;
        }

        if (!is_a($data['nluLogger'] ?? '', NLULogger::class, TRUE)) {
            return 'nlu logger is invalid, should implements '.NLULogger::class;
        }

        return parent::validate($data);
    }


}