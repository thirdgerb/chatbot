<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Blueprint\Internal;

use Commune\Support\Arr\ArrayAndJsonAble;
use Commune\Support\Babel\BabelSerializable;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $chatbotName       机器人的名称.
 * @property-read string $shellName         通讯经过的 Shell. 例如 wechat. 通常平台和 shell 相关
 * @property-read string $shellChatId       会话的唯一ID
 * @property-read string $userId            发送与接受请求的用户 id, 用于追踪唯一用户. 平台相通.
 *
 * @property-read string|null $sessionId    多轮对话的 ID
 * @property-read string $sceneId           请求的场景 ID. 请求可能来自不同的场景.
 * @property-read string $serverId          发送此消息的服务端实例的 ID. 通常是服务器的名字 + port
 * @property-read string $traceId           请求的 traceId. 请求的链式调用追踪的 ID
 */
interface ShellScope extends ArrayAndJsonAble
{
}