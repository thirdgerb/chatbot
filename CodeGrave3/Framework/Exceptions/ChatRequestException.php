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
 * 请求级别的异常. 通知用户告知异常, 并且可以恢复.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ChatRequestException extends \RuntimeException
{
}