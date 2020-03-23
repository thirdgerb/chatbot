<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message;

use Carbon\Carbon;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 所有消息的公共抽象.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Message extends ArrayAndJsonAble
{

    /**
     * 消息创建时间.
     * @return Carbon
     */
    public function getCreatedAt() : Carbon;

    public function interfaces() : array;
}