<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Abstracted;

use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface SoundLike extends ArrayAndJsonAble
{

    public function addSoundLike(string $lang, string $soundLike) : void;

    public function getSoundLike(string $lang) : ? string;

}