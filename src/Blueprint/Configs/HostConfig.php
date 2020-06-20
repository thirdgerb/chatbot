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
 * @property-read string $id
 * @property-read string $name
 *
 * @property-read array $providers              所有 组件共享的服务配置.
 * @property-read array $options
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