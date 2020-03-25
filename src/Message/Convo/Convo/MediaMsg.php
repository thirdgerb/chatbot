<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Convo;

/**
 * 多媒体消息
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface MediaMsg extends ConvoMsg
{
    /**
     * 资源位置的标记
     * @return string
     */
    public function getResource() : string;
}