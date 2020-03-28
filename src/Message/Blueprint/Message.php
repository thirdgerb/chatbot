<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint;

use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Babel\BabelSerializable;
use Commune\Support\DI\Injectable;

/**
 * 所有消息的公共抽象.
 * 并要求可以作为字符串来传输.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Message extends ArrayAndJsonAble, BabelSerializable, Injectable
{

    /**
     * 消息的数据用数组的形式来表示.
     * @return array
     */
    public function getData() : array;

    /**
     * 消息创建时间.
     * @return float
     */
    public function getCreatedAt() : float;

}