<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Message\Convo;

use Commune\Protocals\Message\ConvoMsg;


/**
 * 客户端或服务端不支持的消息.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $type      消息的类型.
 */
interface UnsupportedMsg extends ConvoMsg
{
}