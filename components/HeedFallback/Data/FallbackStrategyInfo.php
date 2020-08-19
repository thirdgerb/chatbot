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

use Commune\Support\Struct\AStruct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $name
 * @property-read string $desc
 * @property-read string $strategyClass
 */
class FallbackStrategyInfo extends AStruct
{
    public static function stub(): array
    {
        return [
            'name' => '',
            'desc' => '',
            'strategyClass' => '',
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}