<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Ghost\Blueprint\Chat;

use Commune\Support\Arr\ArrayAndJsonAble;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 消息的作用域. 作用域由大倒到小排列. 从上往下都是一对多关系.
 *
 * @property-read string $chatbotName       机器人的名称.
 * @property-read string $chatId            会话的唯一ID
 * @property-read string $userId            发送与接受请求的用户 id, 用于追踪唯一用户. 平台相通.
 * @property-read string $sessionId         所属的 Session Id. 一个 Chat 不同时间有多个 Session
 *
 * @property-read string[] $shells          Chat 已经建立的 Shell 通道.
 *  [
 *      shellName => chatId
 *  ]
 */
interface ChatScope extends ArrayAndJsonAble
{

}