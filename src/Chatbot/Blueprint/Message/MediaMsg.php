<?php
/**
 * Created by PhpStorm.
 * User: BrightRed
 * Date: 2019/4/14
 * Time: 6:27 PM
 */

namespace Commune\Chatbot\Blueprint\Message;

/**
 * 多媒体类型的消息.
 *
 * Interface Media
 * @package Commune\Chatbot\Blueprint\Message
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface MediaMsg extends Message
{

    /**
     * 资源都要有一个id, 可以让别的逻辑与之互动.
     * 至于ID具体是什么意思, 可能有很多种.
     * @return string
     */
    public function getMediaId() : string;

}