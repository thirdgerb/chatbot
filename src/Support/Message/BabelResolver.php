<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\Message;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface BabelResolver
{

    /**
     * 序列化一个传输对象
     * @param Transfer $transfer
     * @return string
     */
    public function serialize(Transfer $transfer) : string;

    /**
     * 从字符中反序列化一个传输对象.
     * @param string $serialized
     * @return Transfer|null
     */
    public function unSerialize(string $serialized) : ? Transfer;

}