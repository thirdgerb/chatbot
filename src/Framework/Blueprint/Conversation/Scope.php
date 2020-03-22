<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Conversation;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read string $chatbotName       机器人的名称.
 * @property-read string $sceneId           请求的场景 ID. 请求可能来自不同的场景.
 * @property-read string $messageId         消息的 ID. 消息本身可能有凭替啊的ID
 * @property-read string $traceId           请求的 traceId. 请求的链式调用追踪的 ID
 * @property-read string $userId            发送请求的用户 id, 用于追踪唯一用户. 平台相通.
 * @property-read string $sessionId         所属的会话 ID.
 * @property-read string $platformName      发送请求所属的平台. 例如 wechat
 * @property-read string $shellName         通讯经过的 Shell. 例如 wechat. 通常平台和 shell 相关
 * @property-read string $serverId          服务端实例的 ID. 通常是服务器的名字 + port
 */
interface Scope
{
}