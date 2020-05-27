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

use Commune\Ghost\IMindDef\IIntentDef;
use Commune\Support\Option\AbsMeta;
use Commune\Blueprint\Ghost\MindDef\IntentDef;

/**
 * 意图的元数据. 用于定义标准的意图.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * ## 基础配置
 * @property-read string $name
 * @property-read string $title
 * @property-read string $desc
 * @property-read string $wrapper
 * @property-read array $config
 *
 */
class IntentMeta extends AbsMeta
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            // 意图的名称
            'name' => '',
            // 意图的标题, 应允许用标题来匹配.
            'title' => '',
            // 意图的简介. 可以作为选项的内容.
            'desc' => '',
            // 包装器.
            'wrapper' => IIntentDef::class,
            // 细节配置.
            'config' => [],
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