<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\SpaCyNLU\Configs;

use Commune\Support\Struct\AStruct;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read float $threshold
 * @property-read string $dataPath
 */
class ChatModuleConfig extends AStruct
{
    public static function stub(): array
    {
        return [
            'threshold' => 0.0,
            'dataPath' => ''
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}