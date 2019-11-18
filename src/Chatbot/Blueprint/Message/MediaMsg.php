<?php

namespace Commune\Chatbot\Blueprint\Message;

/**
 * 多媒体类型的消息.
 *
 * Interface Media
 * @package Commune\Chatbot\Blueprint\Message
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface MediaMsg extends ConvoMsg
{

    /**
     * 资源都要有一个url
     * @return string
     */
    public function getUrl() : string;

}