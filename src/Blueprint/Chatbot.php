<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint;

use Psr\Container\ContainerInterface;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Chatbot
{
    /**
     * 系统的公共容器.
     * @return ContainerInterface
     */
    public function getContainer() : ContainerInterface;

    
    /*----- ghost -----*/
    
    public function getGhost(string $name);
}