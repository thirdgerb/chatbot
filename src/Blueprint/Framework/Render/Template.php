<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework\Render;

use Commune\Protocals\Host\ConvoMsg;
use Commune\Protocals\Host\ReactionMsg;


/**
 * 渲染模板.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface Template
{

    /**
     * @param ReactionMsg $msg
     * @return ConvoMsg[]
     */
    public function render(ReactionMsg $msg) : array;
}