<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Platform\SwooleCo\Supports;

use Commune\Support\Option\AbsOption;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $tcpAdapter
 * @property-read float $sendTimeout
 */
class CoTcpAdapterOption extends AbsOption
{
    public static function stub(): array
    {
        return [
            'tcpAdapter' => CoTcpGhostBabelAdapter::class,
            'sendTimeout' => 0.3
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}