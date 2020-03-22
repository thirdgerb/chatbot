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

use Carbon\Carbon;
use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * 平台传输消息的抽象,只用来表征消息本身.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Message extends ArrayAndJsonAble, \Serializable
{

    /**
     * 消息产生的时间
     * @return Carbon
     */
    public function getCreatedAt() : Carbon;


    /**
     * 所有的消息都认为可以有字符串形式的表示
     * @return string
     */
    public function getText() : string;

    /**
     * 可以用于依赖注入的类型
     * @return string[]
     */
    public function getInterfaces() : array;

}