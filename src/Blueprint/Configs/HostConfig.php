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

use Commune\Support\Option\Option;

/**
 * 对话机器人的基础配置
 *
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * 
 * 
 * @property-read string $id                    机器人总的 ID
 * @property-read string $name                  机器人对外暴露的名称. 
 *
 * @property-read array $providers              全局共享的服务配置.
 * @property-read array $options                Host 要绑定的配置单例. 
 *
 * # 关联配置
 * @property-read GhostConfig $ghost            Ghost 的配置
 * @property-read ShellConfig[] $shells         Shell 的配置
 * @property-read PlatformConfig[] $platforms   Platform 的配置
 */
interface HostConfig extends Option
{

    /*------ 获取关联配置 ------*/

    public function getGhostConfig() : GhostConfig;

    public function getShellConfig(string $shellName) : ? ShellConfig;

    public function getPlatformConfig(string $platformId) : ? PlatformConfig;

}