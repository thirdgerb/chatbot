<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Framework\Blueprint\Intercom;

use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * Shell 层输入输出消息 Scope 的封装.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $chatbotName       机器人的名称.
 * @property-read string $shellName         通讯经过的 Shell. 例如 wechat. 通常平台和 shell 相关
 * @property-read string $chatId       会话的唯一ID
 * @property-read string $userId            发送与接受请求的用户 id, 用于追踪唯一用户. 平台相通.
 *
 * @property-read string|null $sessionId    多轮对话的 ID
 * @property-read string $sceneId           请求的场景 ID. 请求可能来自不同的场景.
 */
interface ShellScope extends ArrayAndJsonAble
{
}