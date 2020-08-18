<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Tree\Demo;

use Commune\Blueprint\Framework\Auth\Supervise;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\MindDef\ContextDef;
use Commune\Blueprint\Ghost\MindDef\ContextStrategyOption;
use Commune\Blueprint\Ghost\Mindset;
use Commune\Components\Tree\Impl\SimpleText\STStrategy;
use Commune\Components\Tree\Prototype\TreeContextDef;
use Commune\Ghost\IMindDef\Registers\ContextRegister;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class TreeDemoContext extends ContextRegister
{
    const NAME = 'commune.components.tree.demo';

    public static function selfRegisterToMind(Mindset $mindset, bool $force = false): void
    {
        parent::selfRegisterToMind($mindset, $force);
    }


    public static function makeDef(): ContextDef
    {
        return new TreeContextDef([

            // context 的全名. 同时也是意图名称.
            'name' => self::NAME,
            // context 的标题. 可以用于 精确意图校验.
            'title' => '树形结构对话测试用例',
            // context 的简介. 通常用于 askChoose 的选项.
            'desc' => '树形结构对话测试用例',
            // context 的优先级. 若干个语境在 blocking 状态中, 根据优先级决定谁先恢复.
            'priority' => 1,

            // context 的默认参数名, 类似 url 的 query 参数.
            // query 参数值默认是字符串.
            // query 参数如果是数组, 则定义参数名时应该用 [] 做后缀, 例如 ['key1', 'key2', 'key3[]']
            'queryNames' => [],

            'rootName' => TreeContextDef::FIRST_STAGE,

            // 用一棵树来定义多轮对话结构.
            // 每一个节点都会成为一个 stage.
            // 该 stage 的响应策略则通过 Events 的方式来定义, 实现解耦.
            'tree' => [
                'a' => [
                    'b' => ['c'],
                    'd' => [
                        'e',
                        'f'
                    ],
                ],
                'g',
                'h' => [
                    'j' => [
                        'k'
                    ],
                    'l',
                ],
            ],
            // 树每个分支节点所使用的 Event
            'stageEvents' => [
                Dialog::ANY => STStrategy::class,
            ],
            // 树的节点是否通过 "." 符号连接成 stage_name
            // 例如: [ 'a' => 'b' ] 中的 b 节点, 名字是否为 a.b
            'appendingBranch' => true,

            'strategy' => new ContextStrategyOption([

                'auth' => [Supervise::class],
            ]),
        ]);
    }


}