<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Chatbot\Ghost\Blueprint;

use Commune\Chatbot\Blueprint\Message\Message;

/**
 * Ghost in The Shell
 *
 * 对话机器人的思维内核, 管理所有对话逻辑.
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @property-read Session $session  会话
 * @property-read Mind $mind 思维
 * @property-read Memory $memory 记忆单元
 * @property-read IoC $ioc IoC 容器
 */
interface Ghost
{

    /*-------- speech -------*/

    public function say(Message $message, string $chatId = null) : void;

}