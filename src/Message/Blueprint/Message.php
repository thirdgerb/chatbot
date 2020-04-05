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
 * 基础消息类型
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Message extends ArrayAndJsonAble, BabelSerializable, Injectable
{

    /**
     * @return bool
     */
    public function isEmpty() : bool;

    /**
     * 消息创建时间.
     * @return int
     */
    public function getCreatedAt() : int;

}