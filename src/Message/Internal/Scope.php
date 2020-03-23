<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Message\Internal;


/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 消息的作用域. 作用域由大倒到小排列. 从上往下都是一对多关系.
 *
 * @property-read string $chatbotName       机器人的名称.
 * @property-read string $chatId            会话的唯一ID
 * @property-read string $userId            发送与接受请求的用户 id, 用于追踪唯一用户. 平台相通.
 * @property-read string $sessionId         所属的会话 ID.
 * @property-read string $shellName         通讯经过的 Shell. 例如 wechat. 通常平台和 shell 相关
 * @property-read string $sceneId           请求的场景 ID. 请求可能来自不同的场景.
 * @property-read string $serverId          发送此消息的服务端实例的 ID. 通常是服务器的名字 + port
 * @property-read string $traceId           请求的 traceId. 请求的链式调用追踪的 ID
 * @property-read string $messageId         消息的 ID. 消息本身可能有凭替啊的ID
 */
interface Scope
{
}