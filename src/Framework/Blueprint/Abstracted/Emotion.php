<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Abstracted;

use Commune\Support\Arr\ArrayAndJsonAble;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Emotion extends ArrayAndJsonAble
{
    const POSITIVE = 'positive';

    const NEGATIVE = 'negative';

    public function addEmotion(string $emotionName) : void;

    public function getEmotion() : array;
}