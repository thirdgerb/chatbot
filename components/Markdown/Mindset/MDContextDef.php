<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Mindset;

use Commune\Blueprint\Ghost\MindDef\ContextStrategyOption;
use Commune\Blueprint\Ghost\MindMeta\IntentMeta;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;
use Commune\Ghost\Context\IContext;
use Commune\Ghost\Context\IContextDef;

/**
 * 通过 markdown 文档生成的多轮对话语境.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property string $name
 * @property string $title
 * @property string $desc
 * @property int $priority
 *
 *
 * ## stage 相关定义.
 * @property array $tree
 * @property string $rootName
 * @property string[] $stageEvents
 * @property StageMeta[] $stages
 *
 * ## 属性定义
 * @property array $dependingNames
 * @property string[] $memoryScopes
 * @property array $memoryAttrs
 * @property ContextStrategyOption $strategy
 *
 * ## 意图定义
 * @property IntentMeta $asIntent
 *
 * ## warpper
 *
 * @property string $contextWrapper
 */
class MDContextDef extends IContextDef
{
    const ROOT_STAGE = 'root';

    public static function stub(): array
    {
        return [


            /*----- 核心参数 -----*/

            // context 的全名. 同时也是意图名称.
            'name' => '',
            // context 的标题. 可以用于 精确意图校验.
            'title' => '',
            // context 的简介. 通常用于 askChoose 的选项.
            'desc' => '',
            // context 的优先级. 若干个语境在 blocking 状态中, 根据优先级决定谁先恢复.
            'priority' => 0,

            // context 的默认参数名, 类似 url 的 query 参数.
            // query 参数值默认是字符串.
            // query 参数如果是数组, 则定义参数名时应该用 [] 做后缀, 例如 ['key1', 'key2', 'key3[]']
            'queryNames' => [],

            // tree 的第一级的 key 是各个根节点.
            // 用一棵树来定义多轮对话结构.
            // 每一个节点都会成为一个 stage.
            // 该 stage 的响应策略则通过 Events 的方式来定义, 实现解耦.
            'tree' => [],

            'rootName' => self::ROOT_STAGE,
            // 树每个分支节点所使用的 Event
            'stageEvents' => [],

            // 通过 StageMeta, 而不是 tree 来定义的 stage 组件.
            'stages' => [],


            /*---- 以下为可选参数 ----*/

            // Context 启动时, 会依次检查的参数. 当这些参数都不是 null 时, 认为 Context::isPrepared
            'dependingNames' => [],

            'asIntent' => null,

            // 定义 context 上下文记忆的作用域.
            // 相关作用域参数, 会自动添加到 query 参数中.
            // 作用域为空, 则是一个 session 级别的短程记忆.
            // 不为空, 则是长程记忆, 会持久化保存.
            'memoryScopes' => [],
            // memory 记忆体的默认值.
            'memoryAttrs' => [],

            'strategy' => [
            ],

            // context 实例的封装类.
            'contextWrapper' => IContext::class,
        ];
    }

    public function firstStage(): ? string
    {
        return $this->rootName;
    }

}