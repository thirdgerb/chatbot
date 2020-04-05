<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Exceptions;


/**
 * 需要关闭客户端连接 (如果有长连接) 的异常.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ChatClientException extends AppRuntimeException
{
}