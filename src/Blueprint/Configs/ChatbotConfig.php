<?php

/**
 * This file is part of CommuneChatbot.
 *
 * @link     https://github.com/thirdgerb/chatbot
 * @document https://github.com/thirdgerb/chatbot/blob/master/README.md
 * @contact  <thirdgerb@gmail.com>
 * @license  https://github.com/thirdgerb/chatbot/blob/master/LICENSE
 */

namespace Commune\Blueprint\Configs;


/**
 * 对话机器人的基础配置
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 *
 * @property-read string $chatbotName           机器人的内部名称. 用于在资源内区别各个不同的机器人
 *
 * @property-read GhostConfig[] $ghosts         Ghost 的配置
 * @property-read ShellConfig[] $shells         Shell 的配置
 * @property-read ServerConfig[] $servers       Server 的配置
 */
interface ChatbotConfig
{

}