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
 * # 基础配置
 * @property-read string $chatbotName           机器人的内部名称. 用于在资源内区别各个不同的机器人
 * @property-read bool $isDebugging             是否调试模式.
 *
 *
 * # 关联配置
 * @property-read GhostConfig[] $ghosts         Ghost 的配置
 * @property-read ShellConfig[] $shells         Shell 的配置
 * @property-read PlatformConfig[] $platforms   Platform 的配置
 */
interface HostConfig
{






    /*------ 获取关联配置 ------*/

    public function getShell(string $shellName) : ? ShellConfig;

    public function getGhost(string $ghostName) : ? GhostConfig;

    public function getPlatform(string $platformName) : ? PlatformConfig;

}