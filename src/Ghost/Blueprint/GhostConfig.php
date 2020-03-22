<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint;

use Commune\Support\Struct;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 *
 * @property-read bool $tracking        是否追踪对话逻辑的运算轨迹.
 * @property-read int $stepDepth
 *
 */
class GhostConfig extends Option
{
    public static function stub(): array
    {
        return [

            // 加载各种数据的元数据.
            'metas' => [
            ],

            'components' => [
                // 'componentName',
                // 'componentName' => []
            ],

            'providers' => [
            ],


        ];
    }

}