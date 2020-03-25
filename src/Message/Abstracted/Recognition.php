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
 * 对多媒体信息识别成文字信息
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Recognition extends ArrayAndJsonAble
{
    public function setRecognition(string $text) : void;

    public function getRecognition() : ? string;

}