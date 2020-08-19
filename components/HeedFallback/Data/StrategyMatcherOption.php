<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\HeedFallback\Data;

use Commune\Support\Option\AbsOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $id
 *
 * # 维度. 匹配策略, context/stage/intent, intent, context/stage, context
 *
 * 这意味着要查询四次... 有没有更简单的策略. 或者一次查四个?
 *
 * @property-read string $contextName
 * @property-read string $stageName
 * @property-read string $intent
 *
 * @property-read string $strategyName
 */
class StrategyMatcherOption extends AbsOption
{
    public static function stub(): array
    {
        return [];
    }

    public static function relations(): array
    {
        return [];
    }


}