<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Configs;

use Commune\Support\Option\AbsOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $protocal
 */
class RenderOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            // 分组
            'group' => '',
            // 监听的协议.
            'protocal' => '',
            // 对 render id 使用的过滤器
            'filter' => [
                '*'
            ],
            // 所使用的渲染模板
            'render' => '',
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}