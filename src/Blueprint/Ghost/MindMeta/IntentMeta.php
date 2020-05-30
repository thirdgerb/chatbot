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
 *
 * @property-read string $name
 * @property-read string $title
 * @property-read string $desc
 * @property-read string $wrapper
 *
 * ## 意图内容.
 * @property-read string[] $examples
 *
 * ## 匹配规则
 * @property-read string $signature
 * @property-read string[] $keywords
 * @property-read string[] $regex
 * @property-read string[] $ifEntity
 *
 * @property-read string|null $matcher
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

            // wrapper
            'wrapper' => IIntentDef::class,

            // 意图的标题, 应允许用标题来匹配.
            'title' => '',
            // 意图的简介. 可以作为选项的内容.
            'desc' => '',
            // 例句, 用 []() 标记, 例如 "我想知道[北京](city)[明天](date)天气怎么样"
            'examples' => [],
            // 作为命令.
            'signature' => '',

            // 关键字
            'keywords' => [],
            // 正则
            'regex' => [],

            // 命中任意 entity
            'anyEntity' => [],
            // 自定义校验器. 字符串, 通常是类名或者方法名.
            'matcher' => null,
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