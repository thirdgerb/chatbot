<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Components\SpaCyNLU\Protocols;

use Commune\Support\Struct\AStruct;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $label
 * @property-read float $similarity
 */
class IntentPredictionData extends AStruct
{
    public static function stub(): array
    {
        return [
            'label' => '',
            'similarity' => 0.0,
        ];
    }

    public static function relations(): array
    {
        return [];
    }

    public function getIntentName() : string
    {
        return $this->label;
    }


}