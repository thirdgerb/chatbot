<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Protocals\Host;

use Commune\Protocals\HostMsg;


/**
 * Ghost 对外发表的响应意图.
 * 通常会被解析成多个其它类型的 Message
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 *
 * @property-read string $reactionId        响应的 ID, 不同的 ID 可能会调用不同的解析.
 *
 * @property-read array $slots
 */
interface ReactionMsg extends HostMsg
{
}