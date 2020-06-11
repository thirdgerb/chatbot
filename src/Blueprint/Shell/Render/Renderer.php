<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Shell\Render;

use Commune\Blueprint\Framework\Session;
use Commune\Protocals\HostMsg;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Renderer
{
    /**
     * @return string
     */
    public function getId() : string;

    /**
     * @return string
     */
    public function getTitle() : string;

    /**
     * @param HostMsg $message
     * @param Session $session
     * @return HostMsg[]
     */
    public function render(HostMsg $message, Session $session) : array;

}