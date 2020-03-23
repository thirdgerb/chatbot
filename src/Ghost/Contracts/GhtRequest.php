<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Contracts;

use Commune\Message\Convo\ConvoMsg;
use Commune\Message\Internal\IncomingMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface GhtRequest
{

    /**
     * 检查请求是否合法
     * @return bool
     */
    public function validate() : bool;

    /**
     * 从请求中获取 IncomingMsg
     * @return IncomingMsg
     */
    public function fetchIncoming() : IncomingMsg;


}