<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Framework\Request;

use Commune\Support\Protocal\Protocal;

/**
 * 应用级的协议.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface AppProtocal extends Protocal
{
    /**
     * @return string
     */
    public function getTraceId() : string;

}