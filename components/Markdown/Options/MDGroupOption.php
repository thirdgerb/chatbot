<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\Markdown\Options;

use Commune\Blueprint\Ghost\MindDef\ContextStrategyOption;
use Commune\Components\Tree\Prototype\TreeContextDef;
use Commune\Support\Markdown\Parser\IMDParser;
use Commune\Support\Option\AbsOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $groupName
 * @property-read string $relativePath      文件夹相对路径.
 * @property-read string $namespace
 *
 * @property-read string $markdownParser    markdown parser name
 * @property-read string $contextParser     markdown option to context def
 */
class MDGroupOption extends AbsOption
{
    const IDENTITY = 'groupName';

    public static function stub(): array
    {
        return [
            'groupName' => '',
            'relativePath' => '',
            // 命名空间 + 文件的相对路径 = document id
            'namespace' => '',
            // markdown 默认的分析器.
            'markdownParser' =>  IMDParser::class,

            // 将 option 变成 ContextDef 的工具.
            'contextParser' => '',

            // 根节点的名称.
            'rootName' => TreeContextDef::FIRST_STAGE,

            // stage 使用的策略模块.
            'stageEvents' => [

            ],

            'contextStrategy' => [

            ],
        ];
    }

    public static function relations(): array
    {
        return [
            'contextStrategy' => ContextStrategyOption::class,
        ];
    }


}