<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\HeedFallback;

use Commune\Blueprint\Framework\App;
use Commune\Components\HeedFallback\Data\FallbackStrategyInfo;
use Commune\Components\HeedFallback\Libs\FallbackSceneRepoDemo;
use Commune\Components\HeedFallback\Providers\FallbackStrategyManagerProvider;
use Commune\Components\HeedFallback\Providers\FallbackStrategyRegistryProvider;
use Commune\Components\HeedFallback\Strategies\TransStrategy;
use Commune\Ghost\Component\AGhostComponent;
use Commune\Support\Registry\Meta\StorageMeta;


/**
 * heed fallback 的组件. 全局的 confuse 响应.
 * 当一个消息无法被 stage 处理的时候, 走到 confuse 流程可调用当前组件.
 *
 * 会尝试用已定义的策略来回答用户.
 * 如果没有可选的策略, 则会形成一个任务交给管理员.
 * 管理员完成任务后会存储逻辑, 根据 storage 的特点立刻生效或者延迟生效.
 *
 * 基本的策略:
 *
 *  -> 消息是文本
 *      -> 命中非预期意图 (有 matched)
 *      -> 未命中意图 (无 matched)
 *      -> 答非所问意图
 *
 *  -> 处理逻辑
 *      -> 归档到预期意图
 *          -> 没有后续流程了.
 *      -> 归档到其它意图 (已有意图/新建意图)
 *          -> 将它添加到路由(context/stage)
 *          -> 指定回复内容 ( 策略 )
 *              -> intent 直接回复 (默认)
 *              -> 文本回复
 *              -> Action 回复
 *      -> 忽略 (不可处理)
 *      -> 人工回复 (不记录)
 *      -> 闲聊 (初期可以全局允许)
 *          -> context 允许
 *          -> stage 允许
 *      -> 通用回复
 *          -> stage 通用
 *          -> context
 *
 * 触发任务的原理:
 *  - 未命中任何意图
 *  - 命中意图仍然触发了 confuse
 *  - 用户指定答非所问. (需要记录上轮状态. 通过文本中间件?)
 *
 *
 * 匹配策略:
 *  -> 命中 intent 但没有命中路由
 *     - context/stage/intent
 *     - context/intent
 *     - intent
 *  -> 没有命中 intent (闲聊/指定回复/confuse)
 *     - stage
 *     - context
 * 可以 hash 成 4 个 ID
 *
 *
 * 答非所问策略:
 *
 * - 回合 a, 产生 await 与 routes
 * - 回合 b, 产生用户消息和回复
 * - 回合 c, 提示答非所问.    说明 a 场景的情况和 b 场景的消息都需要记录.
 *
 * - 理想方案是每个回合结束时 (b 轮) 记录上一轮 (c)的场景和这一轮的回复 (或 batchId)
 * - 每个回合开始时则看到 a 轮的路由和 b 轮的回复.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $trans                         文本文件所在路径
 * @property-read string $sceneRepository               记录 fallback 场景的仓库.
 * @property-read FallbackStrategyInfo[] $strategies    系统支持的 fallback 策略.
 * @property-read StorageMeta|null $storage
 * @property-read StorageMeta $initStorage
 */
class HeedFallbackComponent extends AGhostComponent
{
    public static function stub(): array
    {
        return [

            'strategies' => [
                [
                    'name' => '文字回复',
                    'desc' => '使用 translator 模块直接回复',
                    'strategyClass' => TransStrategy::class,
                ],
            ],
            'title' => '上下文回复策略',
            'desc' => '上下文相关回复策略仓库',
            'storage' => null,
            'initStorage' => FallbackStrategyRegistryProvider::defaultStorage(),

            'sceneRepository' => FallbackSceneRepoDemo::class,

            'trans' => __DIR__ . '/resources/trans'
        ];
    }

    public static function relations(): array
    {
        return [
            'storage' => StorageMeta::class,
            'initStorage' => StorageMeta::class,
            'strategies[]' => FallbackStrategyInfo::class,
        ];
    }

    public function bootstrap(App $app): void
    {
        $registry = $app->getServiceRegistry();

        $registry->registerProcProvider(new FallbackStrategyManagerProvider([
            'repositoryConcrete' => $this->sceneRepository,
            'strategies' => $this->strategies,
        ]), false);

        $registry->registerConfigProvider(new FallbackStrategyRegistryProvider([
            'storage' => $this->storage,
            'initStorage' => $this->initStorage
        ]), false);



        $this->loadPsr4MindRegister(
            $app,
            [
                "Commune\\Components\\HeedFallback\\Context" => __DIR__ . '/Context',
                "Commune\\Components\\HeedFallback\\Strategies" => __DIR__ . '/Strategies',
            ]
        );


        // 加载翻译文本资源.
        $this->loadTranslation(
            $app,
            $this->trans,
            true,
            false
        );
    }


}