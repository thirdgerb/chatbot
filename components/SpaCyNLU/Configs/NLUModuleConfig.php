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
 * @property float $threshold
 * @property int $matchLimit
 * @property string $dataPath
 */
class NLUModuleConfig extends AStruct
{
    public static function stub(): array
    {
        return [
            'matchLimit' => 5,
            'threshold' => 0.75,
            'dataPath' => ''
        ];
    }

    public static function relations(): array
    {
        return [];
    }


}