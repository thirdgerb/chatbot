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
 * 会话级别的异常. 需要重置当前会话.
 * @author thirdgerb <thirdgerb@gmail.com>
 */
class ChatSessionException extends AppRuntimeException
{
}