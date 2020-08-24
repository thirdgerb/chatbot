<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Host\Convo\Verbal;

use Commune\Message\Host\Convo\IText;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class JsonMsg extends IText
{

    public static function fromArr(
        array $data
    ) : JsonMsg
    {
        $info = json_encode($data, ArrayAndJsonAble::PRETTY_JSON);
        return static::instance($info);
    }
}