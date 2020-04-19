<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Prototype\Abstracted;

use Commune\Framework\Blueprint\Abstracted\Emotion;
use Commune\Support\Arr\ArrayAbleToJson;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IEmotion implements Emotion
{
    use ArrayAbleToJson;

    public $emotions = [];

    public function toArray(): array
    {
        return [
            'emotions' => $this->getEmotion()
        ];
    }

    public function addEmotion(string $emotionName): void
    {
        $this->emotions[] = $emotionName;
    }

    public function getEmotion(): array
    {
        return array_unique($this->emotions);
    }


}