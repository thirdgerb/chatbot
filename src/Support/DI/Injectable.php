<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Support\DI;


/**
 * 可以作为临时依赖注入的对象.
 * 能够获取多种依赖注入的身份.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Injectable
{

    /**
     * @return string[]
     */
    public function getInterfaces() : array;

}