<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Prototype\Abstracted;

use Commune\Message\Blueprint\Abstracted\Recognition;
use Commune\Support\Arr\ArrayAbleToJson;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class IRecognition implements Recognition
{
    use ArrayAbleToJson;

    public $recognition = null;

    public function toArray(): array
    {
        return [
            'recognition' => $this->recognition
        ];
    }

    public function setRecognition(string $text): void
    {
        $this->recognition = $text;
    }

    public function getRecognition(): ? string
    {
        return $this->recognition;
    }


}