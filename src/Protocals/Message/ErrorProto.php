<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Message;

/**
 * 异常类消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read int $errcode          错误码
 * @property-read string $errmsg        错误描述
 */
interface ErrorProto extends MessageProto
{
}