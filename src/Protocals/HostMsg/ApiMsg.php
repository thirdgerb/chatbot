<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\HostMsg;

use Commune\Blueprint\Exceptions\CommuneErrorCode;
use Commune\Protocals\HostMsg;


/**
 * API 请求使用的消息. 这类消息不需要多轮对话响应, 而是用类似 mvc 框架的方式响应.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
interface ApiMsg extends HostMsg, CommuneErrorCode
{

    public function getApiName() : string;

    public function getParams() : array;
}