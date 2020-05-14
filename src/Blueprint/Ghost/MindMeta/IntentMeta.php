<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Ghost\MindMeta;

use Commune\Support\Option\AbsMeta;
use Commune\Blueprint\Ghost\MindDef\IntentDef;
use Commune\Blueprint\Ghost\MindMeta\Option\ParamOption;

/**
 * 意图的元数据. 用于定义标准的意图.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * ## 基础配置
 * @property-read string $name
 * @property-read string $title
 * @property-read string $desc
 * @property-read string[] $examples
 * @property-read ParamOption[] $entityParams
 *
 * ## 可选
 * @property-read string $asCommand
 * @property-read string $asSpell
 * @property-read string[] $asKeywords
 * @property-read string[] $asRegex
 * @property-read string[] $anyEntity
 */
class IntentMeta extends AbsMeta
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',
            // 例句
            'examples' => [],
            // entity 属性定义.
            'entityParams' => [],

            // 作为命令.
            'asCommand' => '',
            // 作为魔法指令
            'asSpell' => '',
            // 关键字
            'asKeywords' => [],
            // 正则
            'asRegex' => [],
            // 命中任意 entity
            'anyEntity' => [],
            // 自定义校验器.
            'validator' => null,
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public static function validateWrapper(string $wrapper): ? string
    {
        $defType = IntentDef::class;
        return is_a($wrapper, $defType, TRUE)
            ? null
            : static::class . " wrapper should be subclass of $defType, $wrapper given";
    }
}