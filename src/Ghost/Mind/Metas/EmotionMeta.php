<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Mind\Metas;

use Commune\Support\Option\AbsOption;


/**
 * 情感模块的元数据. 用于对多种匹配规则进行合并.
 * 不适合用于遍历所有情感进行主动匹配, 适合用于将已有的匹配信息被动归类
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name                  情感的id.
 * @property-read string $title                 情感的标题
 * @property-read string $desc                  情感的简介
 * @property-read string[] $implements
 * @property-read string[] $emotionalIntents    符合该情感的各种意图名称.
 * @property-read string[] $matchers            自定义的匹配逻辑.
 */
class EmotionMeta extends AbsOption
{
    const IDENTITY = 'name';

    public static function stub(): array
    {
        return [
            'name' => '',
            'title' => '',
            'desc' => '',
            'implements' => [],
            'emotionalIntents' => [],
            'matchers' => [],
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}