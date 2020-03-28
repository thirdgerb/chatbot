<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint\Convo;

/**
 * 用 Json 的方式传递的数据.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface JsonMsg extends ConvoMsg
{

    /**
     * Json 字符串.
     * @return string
     */
    public function getJson() : string;

    public function getJsonData() : array;

}