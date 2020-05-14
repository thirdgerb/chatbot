<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Context;

use Commune\Blueprint\Ghost\MindMeta\MemoryMeta;
use Commune\Blueprint\Ghost\MindMeta\Option\ParamOption;
use Commune\Blueprint\Ghost\MindMeta\StageMeta;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * ## 基础属性
 *
 * @property-read bool $public
 * @property-read int $priority         语境的默认优先级
 *
 * ## context wrapper
 * @property-read string|null $contextWrapper       Context 的包装器.
 *
 * ## 属性相关
 * @property-read ParamOption[] $queryParams
 * @property-read string[] $entities
 *
 * ## Stage 相关
 * @property-read StageMeta $asStage                Stage 的定义.
 * @property-read MemoryMeta $asMemory              Memory 的定义
 * @property-read StageMeta[] $stages               Context 定义的 Stages
 *
 */
class IContextDef extends ContextDefPrototype
{
    public static function stub(): array
    {
        return [
            'priority' => 0,
            'public' => true,
            'contextWrapper' => null,
            'queryParams' => [],
            'asStage' => [],
            'asMemory' => [],
            'entities' => [],
            'stages' => [],
        ];
    }


}